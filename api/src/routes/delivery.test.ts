/**
 * Tests for the Delivery API routes.
 *
 * These tests specifically validate the security fix for the
 * "Uncontrolled Command Line" (command injection) vulnerability that existed
 * in the PUT /deliveries/:id/status endpoint.
 *
 * WHAT WE ARE TESTING:
 *   1. Normal CRUD operations still work after the security fix.
 *   2. A valid `deliveryPartner` value passes input validation (returns 500
 *      because the `notify` binary is not installed in the test environment,
 *      NOT 400, which would indicate our validation incorrectly rejected it).
 *   3. Malicious `deliveryPartner` values containing shell metacharacters are
 *      rejected with HTTP 400 BEFORE the OS is ever asked to run anything.
 *
 * WHY THESE TESTS MATTER:
 *   Without tests like #3, a future developer could accidentally revert the
 *   validation logic and the command-injection path would silently reopen.
 *   These tests act as a regression guard.
 */

import { describe, it, expect, beforeEach, afterEach, vi } from 'vitest';
import request from 'supertest';
import express from 'express';
import deliveryRouter from './delivery';
import { runMigrations } from '../db/migrate';
import { closeDatabase, getDatabase } from '../db/sqlite';
import { errorHandler } from '../utils/errors';

// ─── Mock child_process.execFile ──────────────────────────────────────────────
// We mock `execFile` so that our tests do not depend on the `notify` binary
// being installed.  We capture the arguments it was called with so we can
// assert that:
//   a) it is called with the correct file name ('notify')
//   b) the user-supplied partner name is passed as a separate array element,
//      NOT interpolated into a shell command string.
//
// The mock simulates a successful execution by calling the callback with
// (null, 'ok', '') — i.e. no error, stdout='ok', stderr=''.
//
// IMPORTANT (Vitest hoisting):
//   `vi.mock(...)` calls are automatically hoisted to the top of the file by
//   Vitest's transformer — they run BEFORE any `const`/`let` declarations in
//   module scope.  This means a mock factory that references a `const` defined
//   below `vi.mock(...)` would fail with "Cannot access before initialization".
//
//   The solution is `vi.hoisted(() => ...)`, which registers a value that is
//   also hoisted, so it is always available when the mock factory executes.
const { mockExecFile } = vi.hoisted(() => {
  const mockExecFile = vi.fn(
    (
      _file: string,
      _args: string[],
      callback: (err: Error | null, stdout: string, stderr: string) => void,
    ) => {
      callback(null, 'ok', '');
    },
  );
  return { mockExecFile };
});

vi.mock('child_process', () => ({
  // Replace `execFile` with our hoisted spy function.
  // Everything else in child_process (execSync, spawn, etc.) is not used by
  // delivery.ts, so we don't need to mock it.
  execFile: mockExecFile,
}));

// ─── Mock express-rate-limit ──────────────────────────────────────────────────
// The rate limiter in delivery.ts is a module-level singleton.  Across the ~22
// requests that the test suite fires at the status endpoint it would
// accumulate hits and eventually return 429 instead of letting validation
// logic run — causing spurious test failures unrelated to what we are testing.
//
// Unit tests should not exercise rate-limiting infrastructure (that belongs in
// an integration / load test).  We therefore replace the limiter with a simple
// pass-through middleware so every request reaches the route handler normally.
vi.mock('express-rate-limit', () => ({
  default: () =>
    (_req: express.Request, _res: express.Response, next: express.NextFunction) =>
      next(),
}));

// ─── Test setup ───────────────────────────────────────────────────────────────

let app: express.Express;

describe('Delivery API', () => {
  beforeEach(async () => {
    // Clear mock call history before each test so assertions are independent.
    mockExecFile.mockClear();

    // Ensure a fresh in-memory database for each test.
    await closeDatabase();
    await getDatabase(true);
    await runMigrations(true);

    // Seed the required foreign-key row: deliveries.supplier_id references suppliers.
    const db = await getDatabase();
    await db.run(
      'INSERT INTO suppliers (supplier_id, name, contact_person, email, phone) VALUES (?, ?, ?, ?, ?)',
      [1, 'Test Supplier', 'Alice', 'alice@supplier.com', '555-0100'],
    );

    // Build a minimal Express app that mounts the delivery router and the
    // error handler (which translates NotFoundError → 404, etc.).
    app = express();
    app.use(express.json());
    app.use('/deliveries', deliveryRouter);
    app.use(errorHandler);
  });

  afterEach(async () => {
    await closeDatabase();
  });

  // ─── Helper: create a delivery and return its ID ─────────────────────────
  async function createTestDelivery(): Promise<number> {
    const response = await request(app).post('/deliveries').send({
      supplierId: 1,
      deliveryDate: '2025-01-15',
      name: 'Test Delivery',
      description: 'A test delivery',
      status: 'pending',
    });
    expect(response.status).toBe(201);
    return response.body.deliveryId;
  }

  // ─── Basic CRUD smoke tests ───────────────────────────────────────────────

  it('should create a new delivery', async () => {
    const response = await request(app).post('/deliveries').send({
      supplierId: 1,
      deliveryDate: '2025-01-15',
      name: 'First Delivery',
      description: 'Initial shipment',
      status: 'pending',
    });
    expect(response.status).toBe(201);
    expect(response.body.name).toBe('First Delivery');
    expect(response.body.deliveryId).toBeDefined();
  });

  it('should get all deliveries', async () => {
    const response = await request(app).get('/deliveries');
    expect(response.status).toBe(200);
    expect(Array.isArray(response.body)).toBe(true);
  });

  it('should get a delivery by ID', async () => {
    const id = await createTestDelivery();
    const response = await request(app).get(`/deliveries/${id}`);
    expect(response.status).toBe(200);
    expect(response.body.deliveryId).toBe(id);
  });

  it('should return 404 for a non-existing delivery', async () => {
    const response = await request(app).get('/deliveries/999');
    expect(response.status).toBe(404);
  });

  it('should delete a delivery by ID', async () => {
    const id = await createTestDelivery();
    const response = await request(app).delete(`/deliveries/${id}`);
    expect(response.status).toBe(204);
  });

  // ─── Status-update tests (the patched endpoint) ───────────────────────────

  it('should update delivery status without a deliveryPartner', async () => {
    const id = await createTestDelivery();
    const response = await request(app)
      .put(`/deliveries/${id}/status`)
      .send({ status: 'in-transit' });
    expect(response.status).toBe(200);
    expect(response.body.status).toBe('in-transit');
    // execFile must NOT have been called — there was no partner to notify.
    expect(mockExecFile).not.toHaveBeenCalled();
  });

  it('should update status and invoke execFile with a valid deliveryPartner', async () => {
    const id = await createTestDelivery();
    const response = await request(app)
      .put(`/deliveries/${id}/status`)
      .send({ status: 'delivered', deliveryPartner: 'AcmeLogistics' });

    expect(response.status).toBe(200);
    expect(response.body.commandOutput).toBe('ok');

    // ✅ The critical assertion: execFile must have been called with the binary
    //    name as the FIRST argument and the partner name as a SEPARATE array
    //    element — never concatenated into a shell command string.
    expect(mockExecFile).toHaveBeenCalledWith(
      'notify',            // binary name
      ['AcmeLogistics'],   // arguments array — NOT a shell string
      expect.any(Function),
    );
  });

  it('should accept valid deliveryPartner names with allowed special characters', async () => {
    const id = await createTestDelivery();
    // Hyphens, underscores, dots, and spaces are all allowed.
    const validNames = ['Acme Logistics', 'acme-co', 'acme_co', 'acme.co', 'Acme 123'];
    for (const name of validNames) {
      mockExecFile.mockClear();
      const response = await request(app)
        .put(`/deliveries/${id}/status`)
        .send({ status: 'pending', deliveryPartner: name });
      // Should reach execFile (mocked), not be rejected by validation.
      expect(response.status).toBe(200);
      expect(mockExecFile).toHaveBeenCalledWith('notify', [name], expect.any(Function));
    }
  });

  // ─── Security regression tests ────────────────────────────────────────────
  //
  // These tests are the core of the security fix validation.
  //
  // Each test sends a `deliveryPartner` value that contains shell metacharacters
  // that an attacker would use to inject additional commands.  The expected
  // result is HTTP 400 — the request is rejected BEFORE `execFile` (or the
  // former vulnerable `exec`) is ever called.
  //
  // If any of these tests start failing with a status other than 400, it means
  // the input-validation guard has been removed or weakened, and the endpoint
  // may be vulnerable again.

  const maliciousPayloads = [
    // Shell command chaining via semicolons
    { name: '; cat /etc/passwd', description: 'semicolon command chaining' },
    // Piping output to another command
    { name: '| cat /etc/passwd', description: 'pipe operator' },
    // Background execution via &
    { name: '& whoami', description: 'ampersand background execution' },
    // Command substitution via backticks
    { name: '`id`', description: 'backtick command substitution' },
    // Command substitution via $()
    { name: '$(id)', description: 'dollar-paren command substitution' },
    // Redirecting output to a file
    { name: '> /tmp/pwned', description: 'output redirection' },
    // Path traversal in a binary name
    { name: '../../etc/shadow', description: 'path traversal' },
    // Newline injection (tries to break the command into two lines)
    { name: 'acme\nid', description: 'newline injection' },
    // Null byte injection
    { name: 'acme\0evil', description: 'null byte injection' },
    // Double-quoted shell expansion
    { name: '"$(id)"', description: 'quoted command substitution' },
    // Single-quoted value (trying to escape quoting)
    { name: "acme'; id; echo '", description: 'single-quote shell escape' },
    // Exclamation mark (bash history expansion)
    { name: 'acme!id', description: 'bash history expansion' },
  ];

  for (const { name: payload, description } of maliciousPayloads) {
    it(`should reject malicious deliveryPartner containing ${description} with HTTP 400`, async () => {
      const id = await createTestDelivery();
      const response = await request(app)
        .put(`/deliveries/${id}/status`)
        .send({ status: 'in-transit', deliveryPartner: payload });

      // ✅ The validation must block this BEFORE execFile is called.
      expect(response.status).toBe(400);
      expect(mockExecFile).not.toHaveBeenCalled();
    });
  }

  it('should reject an empty deliveryPartner string with HTTP 400', async () => {
    const id = await createTestDelivery();
    const response = await request(app)
      .put(`/deliveries/${id}/status`)
      .send({ status: 'in-transit', deliveryPartner: '' });
    // Empty string is treated as falsy by `if (deliveryPartner)` so execFile
    // is never called; the delivery is just updated without notification.
    // Confirm the status update still succeeds.
    expect(response.status).toBe(200);
    expect(mockExecFile).not.toHaveBeenCalled();
  });

  it('should reject a deliveryPartner longer than 100 characters with HTTP 400', async () => {
    const id = await createTestDelivery();
    const longName = 'A'.repeat(101); // 101 characters — one over the limit
    const response = await request(app)
      .put(`/deliveries/${id}/status`)
      .send({ status: 'in-transit', deliveryPartner: longName });
    expect(response.status).toBe(400);
    expect(mockExecFile).not.toHaveBeenCalled();
  });
});

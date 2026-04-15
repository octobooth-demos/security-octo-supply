# Agent Hooks Demo

This walkthrough demonstrates how to use **Agent Hooks** to automate workflows and enforce governance policies in the AI-assisted development process.

## What are Agent Hooks?

Agent Hooks allow execution of arbitrary commands or scripts at specific points in the Copilot Agent lifecycle. These hooks enable sophisticated workflows such as:

- **Audit Logging**: Recording prompts and session events for compliance.
- **Automated Checkpoints**: Creating git commits after each agent session.
- **Validation**: Running linters or tests before allowing the agent to proceed.

Hooks are defined in `.github/hooks/hooks.json` and support lifecycle events including `sessionStart`, `UserPromptSubmit`, and `Stop`.

## Demo 1: Audit Logging

- **What to Show:** How Start-, Prompt- and Stop-Hooks log agent activity to a file.
- **Why:** Demonstrate the extensibility of Agent Hooks. This simple file-logging example illustrates how organizations can integrate Agent activity with their internal compliance systems, metrics dashboards, or governance tools.
- **How:**
  1. Open `.github/hooks/hooks.json` and explain the `sessionStart`, `UserPromptSubmit` and `Stop` hook configuration.
  2. Open Copilot Chat (Agent Mode) and ask a prompt like:

     ```txt
     What frameworks are used in this project?
     ```

  3. Open `.agent.log` (or run `cat .agent.log` in terminal) to show the captured timestamped entries.

## Demo 2: Automated Checkpoints

- **What to Show:** Enable the "checkpoint commit" hook and show how it can automatically safeguards work at the end of every agent session.
- **Why:** Demonstrate how hooks can smoothen the developer experience by providing flow mechanisms like auto-commits.

### Option 1: Quick Visual Demo

- **How:**
  1. Open `.github/hooks/hooks.json`.
  2. Locate the `Stop` event section and **uncomment** the `checkpoint-commit.sh` block:

     ```jsonc
     // comment this in if you want automatic commits after each agent session
     {
       "type": "command",
       "bash": ".github/hooks/checkpoint-commit.sh",
       "cwd": ".",
       "timeoutSec": 10
     }
     ```

  3. Ask Copilot to make a visible change:

     ```
     Add two rocket emojis 🚀🚀 to the title in the README.
     ```

  4. Wait for the agent to finish.
  5. Show the **Source Control** view or run `git log -1` to verify a new commit exists with the timestamp and message.

### Option 2: Complex Workflow (With Skills)

- **How:**
  1. Ensure the checkpoint hook is enabled (see Option 1).
  2. Go through the [Agent Skills](./agent-skills.md) walkthrough to set up the `api-endpoint` skill.
  3. Point out that the massive amount of generated code (Models, Routes, Tests) is neatly encapsulated in a single "checkpoint" commit, separating it from previous work.

## Key Takeaways

| Feature                | Benefit                                                                                 |
| :--------------------- | :-------------------------------------------------------------------------------------- |
| **Audit Logs**         | Automatic compliance and usage tracking without developer intervention.                 |
| **Checkpoint Commits** | "Save game" functionality for AI coding. Never lose good code; easily undo bad code.    |
| **Extensibility**      | Allows sophisticated and deterministic execution for all lifecycles of an agent session |

---

## Related Resources

- [Agent Skills](./agent-skills.md)
- [Copilot Custom Instructions](./copilot.md)

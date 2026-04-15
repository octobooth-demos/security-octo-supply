# **GitHub Agentic Workflows Demo**

This demo walks through GitHub Agentic Workflows (`gh-aw`) - AI-powered automation that runs directly in GitHub Actions.

- [**GitHub Agentic Workflows Demo**](#github-agentic-workflows-demo)
  - [What Are Agentic Workflows?](#what-are-agentic-workflows)
    - [Value Proposition](#value-proposition)
  - [Important: Time Investment](#important-time-investment)
  - [Setup Requirements](#setup-requirements)
    - [Token Configuration](#token-configuration)
      - [COPILOT\_GITHUB\_TOKEN (Copilot Authentication)](#copilot_github_token-copilot-authentication)
      - [GH\_AW\_AGENT\_TOKEN (Agent Assignment)](#gh_aw_agent_token-agent-assignment)
  - [Authoring Workflows](#authoring-workflows)
    - [Creating New Workflows](#creating-new-workflows)
    - [Updating Existing Workflows](#updating-existing-workflows)
    - [Key Concepts](#key-concepts)
      - [Custom Front matter - e.g. Schedule Jittering](#custom-front-matter---eg-schedule-jittering)
      - [Secure Sandbox and Permissions](#secure-sandbox-and-permissions)
      - [Safe Outputs](#safe-outputs)
  - [Demo Scenarios](#demo-scenarios)
    - [Demo 1: Create a New Workflow from Scratch](#demo-1-create-a-new-workflow-from-scratch)
    - [Demo 2: Auto-Analyze Build Failures](#demo-2-auto-analyze-build-failures)
    - [Demo 3: Daily Repo Activity Summary](#demo-3-daily-repo-activity-summary)
  - [Summary: Key Takeaways](#summary-key-takeaways)

---

## What Are Agentic Workflows?

Agentic Workflows are AI-powered GitHub Actions workflows that can reason, make decisions, and take actions autonomously. Unlike traditional YAML-based workflows that follow rigid, predefined steps, agentic workflows are written in markdown and use natural language to describe what the agent should accomplish.

The agent interprets the instructions, gathers context, and dynamically determines the best path to achieve the goal - including error handling, retries, and adaptive decision-making. Execution is secured within a sandbox with explicit permissions and safe outputs to ensure security.

### Value Proposition

| Traditional Workflows            | Agentic Workflows                 |
| -------------------------------- | --------------------------------- |
| Rigid step-by-step execution     | Dynamic, goal-oriented execution  |
| Fails on unexpected conditions   | Adapts and self-heals             |
| Requires explicit error handling | Intelligent error recovery        |
| Complex YAML configurations      | Natural language instructions     |
| Limited to predefined actions    | Can reason about novel situations |

**Key Benefits:**

- **Reduced Maintenance**: Agents adapt to changes without manual updates
- **Faster Authoring**: Natural language is faster than complex YAML
- **Intelligent Automation**: Handle edge cases and unexpected scenarios
- **Self-Healing**: Automatically recover from transient failures
- **Integrated with GitHub**: Native access to issues, PRs, Actions, and more

---

## Important: Time Investment

> [!CAUTION]
> **Presenter Note:** Agentic workflows take significantly longer to create and test than traditional workflows. Plan for:
>
> - **Initial Creation**: 10-30 minutes for CCA to generate and validate a workflow
> - **Testing & Iteration**: Multiple runs to refine behavior and permissions
> - **Demo Prep**: Start the "create new workflow" demo first, then show existing workflows while it runs

The workflows in this repo have been pre-created and tested. If you're demoing live workflow creation, start it early and pivot to existing examples.

---

## Setup Requirements

Before using Agentic Workflows, ensure the repository has the required tokens configured.

### Token Configuration

Refer to the [official token documentation](https://github.github.com/gh-aw/reference/auth/) for complete details. For these demos, the following tokens are required:

#### COPILOT_GITHUB_TOKEN (Copilot Authentication)

This token authenticates the Copilot agent to run within GitHub Actions.

- **Documentation**: [COPILOT_GITHUB_TOKEN Reference](https://github.github.com/gh-aw/reference/auth/#copilot_github_token)
- **Setup**: This is typically provided automatically by the GitHub Actions environment when Copilot is enabled for the organization/repository

#### GH_AW_AGENT_TOKEN (Agent Assignment)

This token allows workflows to assign issues to the Copilot Coding Agent.

- **Documentation**: [GH_AW_AGENT_TOKEN Reference](https://github.github.com/gh-aw/reference/auth/#gh_aw_agent_token)
- **Setup**: Create a Personal Access Token with appropriate permissions and add it as a repository secret named `GH_AW_AGENT_TOKEN`
- **Required For**: Workflows that use `assign-to-agent` safe outputs (like `auto-analyze-failures.md`)

> [!TIP] Once we get organization-level support for these tokens, setup will be simplified since we will no longer need to configure tokens.

---

## Authoring Workflows

> [!IMPORTANT]
> **Golden Rule:** Users should never edit the markdown workflow files directly. Always use Copilot Coding Agent (CCA) to create or modify workflows. Of course, power users may edit the files manually, but the golden path is to use the agentic workflow custom agent to update workflows.

The primary reference for authoring workflows is: [Creating Workflows](https://github.github.io/gh-aw/setup/creating-workflows/)

### Creating New Workflows

Use CCA to "vibe-code" your workflows:

1. Open the repo and navigate to the Agents tab
2. Describe the workflow you want to create and format the prompt like this:

3. ```text
    Create a workflow for GitHub Agentic Workflows using https://github.com/github/gh-aw/blob/main/create.md
    The purpose of the workflow is to <x>
    ```

    where `<x>` describes the goal of the workflow in natural language.
4. Let the agent cook!

### Updating Existing Workflows

To update or convert traditional YAML workflows to agentic workflows:

1. Open the repo and navigate to the Agents tab
2. Select the **agentic-workflows** custom agent
3. Describe the changes or conversion needed
4. The agent will update the workflow while preserving intent

> [!TIP] You can show the "old" YML auto analyze workflow and then show the updated agentic workflow to highlight the differences in structure and capabilities. The agentic workflow was created by asking the agent to convert the YAML workflow to an agentic workflow.

### Key Concepts

#### Custom Front matter - e.g. Schedule Jittering

Agentic workflow markdown file have custom frontmatter properties that look similar to Actions properties. There are differences though: for example, `schedule: daily` runs at a **random time** each day. This "jittering" prevents multiple workflows from executing simultaneously and overwhelming resources.

```yaml
on:
  schedule: daily  # Runs at a random time each day
```

#### Secure Sandbox and Permissions

Agentic workflows run in a **secure sandbox** with minimal default permissions:

- **Network isolation**: Only explicitly allowed domains can be accessed
- **Read-only by default**: Write permissions must be explicitly granted
- **Tool restrictions**: Only specified tools and commands are available

Example permission block:

```yaml
permissions:
  contents: read
  issues: read
  pull-requests: read
network:
  allowed:
    - node  # Allow npm registry access
```

#### Safe Outputs

Safe outputs are a security feature that restricts what actions the agent can take. Instead of having full write access, agents declare specific outputs they can produce:

```yaml
safe-outputs:
  create-issue:
    title-prefix: "[auto] "  # All created issues must have this prefix
    max: 1                    # Only one issue per run
  assign-to-agent:
    allowed: [copilot]        # Can only assign to Copilot
  noop:                       # Allow "no operation" output
```

Refer to the [Safe Outputs documentation](https://github.github.io/gh-aw/reference/safe-outputs/) for complete details.

---

## Demo Scenarios

### Demo 1: Create a New Workflow from Scratch

> [!NOTE]
> This takes 10-30 minutes to complete. **Start this first**, then demo the existing workflows while it runs.

**What to show:** Using CCA to create an agentic workflow from scratch.

**Why:** Demonstrate the "vibe-coding" approach to workflow authoring.

**How:**

1. Open Copilot Chat in the repo Agents tab
2. Enter this prompt:

   ```text
   Create a workflow for GitHub Agentic Workflows using https://github.com/github/gh-aw/blob/main/create.md. The purpose of the workflow is to import multi-device resolution tester agentic workflow from github/gh-aw and adapt it to test the website in this repo.
   ```

3. Let CCA cook - it will:
   - Fetch the creation guide
   - Create the markdown file describing the intent of the workflow
   - Create the workflow file in `.github/workflows/`

4. While waiting, proceed to Demo 2 and Demo 3

5. Once complete, review the generated workflow:
   - Check the frontmatter (permissions, tools, safe-outputs)
   - Review the natural language instructions
   - Show how it references `${{ github.xxx }}` context variables

**Key Takeaway:** Natural language prompts + CCA = rapid workflow creation without YAML expertise

---

### Demo 2: Auto-Analyze Build Failures

**What to show:** Automatic failure analysis and issue creation

**Why:** Demonstrate intelligent, autonomous DevOps automation.

**Setup:** The workflow [auto-analyze-failures.md](../../.github/workflows/auto-analyze-failures.md) is already configured.

> [!WARNING]
> The generated workflow `auto-analyze-failures.lock.yml` is disabled by default to avoid errors as long as the tokens are not configured.
>
> **To enable it:**
>
> 1. Go to the **Actions** tab in your repository
> 2. Select **"Auto Analyze Build Failures"** from the left sidebar
> 3. Click the **Enable workflow** button

**How:**

1. Create an intentional failure in the code as follows:
   1. Hit *Cmd-shift-P* and select `Tasks: Run Task` and then select `Copilot: Self-healing DevOps`.
   2. This will create a breaking code change.
   3. Commit and push this change and create a PR to trigger the CI workflow, which should fail.
2. Wait for the CI build to fail (this should take a few moments)
3. The `auto-analyze-failures` workflow automatically triggers on the failure
4. Show the workflow run:
   - The agent reads the failure logs
   - Classifies the failure type (code, test, config, dependency, etc.)
   - Determines if it's transient or requires action
5. If not transient, an issue is automatically created with:
   - Failure analysis summary
   - Remediation plan
   - Links to the failed run
6. For certain failure categories, the issue is automatically assigned to Copilot Coding Agent
7. Open Issues tab to show the newly created issue

**Show the workflow code:**

```markdown
## Analysis guidance

Classify the failure into one category:
- `code`
- `test`
- `config`
- `dependency`
- `infrastructure`
- `quality`
- `repeat-transient`

Determine if it is transient (re-running would likely succeed).
```

**Key Takeaway:** Agentic workflows can triage failures, create actionable issues, and even assign them to Copilot for automatic remediation.

---

### Demo 3: Daily Repo Activity Summary

**What to show:** Scheduled autonomous reporting

**Why:** Demonstrate scheduled agentic automation and repository insights.

**Setup:** The workflow [daily-repo-activity-summary.md](../../.github/workflows/daily-repo-activity-summary.md) is already configured.

**How:**

1. Navigate to **Actions** tab in GitHub
2. Find the workflow: **"Daily Repo Activity Summary"**
3. Click **"Run workflow"** to trigger manually
   - Note: In production, this runs daily at a random time (jittered)
4. Wait for completion (~2-5 minutes)
5. Check the **Issues** tab for the new summary issue
6. Show the generated content:
   - Issues opened/closed in last 24 hours
   - Pull requests activity
   - Notable high-activity items
   - Direct links to all referenced items

**Show the workflow code:**

```markdown
## Output Requirements

- Title should use the configured prefix and be concise (e.g., "[daily activity] 2026-02-04 Repo Summary")
- Body must follow the reporting format guidelines:
  - Use `###` headers or lower
  - Provide a summary section with key counts
  - Use `<details>` sections for lists of issues and PRs
```

**Point out the safe-outputs constraint:**

```yaml
safe-outputs:
  create-issue:
    title-prefix: "[daily activity] "
    max: 1  # Only one issue per run
```

**Key Takeaway:** Agentic workflows can autonomously gather data, synthesize insights, and create structured reports on a schedule.

---

## Summary: Key Takeaways

| Topic           | Takeaway                                               |
| --------------- | ------------------------------------------------------ |
| **What**        | AI-powered workflows that reason and adapt             |
| **Why**         | Faster authoring, intelligent automation, self-healing |
| **How**         | Natural language Markdown, not YAML                    |
| **Authoring**   | Always use CCA, never edit directly                    |
| **Security**    | Secure sandbox, explicit permissions, safe-outputs     |
| **Scheduling**  | Jittered execution prevents resource conflicts         |
| **Integration** | Native access to GitHub APIs and tools                 |

**Next Steps:**

- Explore the [gh-aw documentation](https://github.github.io/gh-aw/)
- Review existing workflows in `.github/workflows/*.md`
- Create custom workflows for your team's needs

# Copilot Coding Agent & Mission Control

## Copilot Coding Agent

### Demo: Using `/handoff` Custom Prompt for Session Management

- **What to show:** Using the custom `/handoff` prompt to hand off Ask/Agent work to another session with proper context preservation.
- **Why:** Demonstrate how custom prompts can control context, drop unnecessary information, and efficiently hand off work between Chat/Agent sessions or team members.
- **How:**  
  1. Open Copilot Chat and switch to `Plan` mode.
  2. Enter `I want to add Personal Profile page to the app that shows the user profile and their purchases.`
  3. Show the output and ask Copilot to change something in the plan: for example, remove the `purchases` part
  4. **Explain the Context Problem**: Currently the entire conversation is in the context, which over time grows long and can consume too much of the context window. Custom prompts can solve this by creating clean handoffs.
  5. **Show the Custom Prompt**: Open the [handoff.prompt.md](../../.github/prompts/handoff.prompt.md) file in the prompts directory. Point out:
     - The YAML frontmatter configuring it as an Agent mode prompt
     - The internal thinking process in HTML comments (not shown to user)
     - The structured template for consistent handoffs
  6. **Run the Prompt**: Click the "Run" button, use Command Palette → "Prompts: Run Prompt" or type `/handoff` in the chat to execute the handoff prompt
  7. **Show Results**: Display the generated `handoff.md` document. It should contain:
     - Clean summary without noise from the conversation
     - Gathered information and requirements
     - The refined plan (without the removed `purchases` part)
     - Next actions for the receiving developer
  8. **Complete the Handoff**: Switch to `Agent` mode, include the handoff document as context, and ask Copilot to `implement the changes according to the handoff document`. You can cancel after a few seconds since you don't need to show the entire implementation.
  9. **Best Practices**: Explain that custom handoff prompts are valuable for:
     - Context size management
     - Clean knowledge transfer between sessions
     - Team collaboration and handoffs
     - Preserving important decisions while removing noise
  10. **Cleanup**: You can revert the changes to the `handoff.md` file after the demo.

### Demo: Using `/handoff-to-copilot-coding-agent` Custom Prompt for Async Session Continuation

- **What to show:** Using the custom `/handoff-to-copilot-coding-agent` prompt to hand off current plan work to GitHub Copilot Coding Agent with proper context preservation.
- **Why:** Demonstrate how custom prompts can encapsulate IDE tools and MCP tools calls into a cohesive workflow.

- **How:**  
  1. Make sure that you have Remote GitHub MCP Server running.
  2. Open Copilot Chat and switch to `Plan` Chat Mode.
  3. Enter `I want to add Personal Profile page to the app that shows the user profile and their purchases.`
  4. Show the output and ask Copilot to change something in the plan: for example, remove the `purchases` part
  5. **Explain Time Constraints**: We have a detailed plan now, Copilot Agent can follow it and implement the desired feature, however, in order to use our time efficiently we can hand off the implementation to the Copilot Agent, allowing us to focus on other tasks (or showing other copilot features in this demo).
  6. **Show the Custom Prompt**: Open the [handoff-to-copilot-coding-agent.prompt.md](../../.github/prompts/handoff-to-copilot-coding-agent.prompt.md) file in the prompts directory. Point out:
     - The YAML frontmatter configuring it as an Agent mode prompt
     - The internal thinking process in HTML comments (not shown to user)
     - The structured issue template for consistent handoffs
     - Use of tools like `changes`, `create_issue`, and `assign_copilot_to_issue`.
     - Show how to configure the tools (click 'Configure Tools' link above `tools: []` line)
  7. **Run the Prompt**:
      - _Important_ We're in the 'Plan' Chat Mode now, and it has a limited set of tools available. We need to switch to `Agent` mode to use /handoff-to-copilot-coding-agent prompt. At the moment we cannot force switch the mode.
      - Click the "Run" button, use Command Palette → "Prompts: Run Prompt" or type `/handoff-to-copilot-coding-agent` to execute the handoff prompt
  8. **Show Results**: Display the generated output, it should contain a call to GitHub MCP and a short summary with the Issue link.
     - Clean summary without noise from the conversation
     - Gathered information and requirements
     - The refined plan (without the removed `purchases` part)
  
      Open GitHub repository and show the new issue. Demonstrate that it's been assigned to GitHub Copilot Coding Agent and it started the session.

      > [!TIP]
      > **Using 3rd Party Agents:** When viewing the issue, you can select **Claude Code** or **Codex** as the agent to handle the implementation instead of the default Copilot Coding Agent.

  9. **Complete the Handoff**: You can now stop the session if you don't need this implementation for your demo.
  10. **Best Practices**: Explain that custom prompts are valuable for:
  - Codifying repetitive parts of existing workflows
  - Improving the discoverability of available Copilot use cases

### Demo: Using Copilot to help you help Copilot (inception)

- **What to show:** Using a Chat Mode to help you refine your prompt, including a clarity score
- **Why**: Helping users clarify their prompts is key to getting good results: but most developers don't know how to improve their prompts. This custom Chat Mode helps to improve prompts.
- **How:**
  1. Run `/refine-prompt` prompt
  2. Enter a vague prompt: `I want a Cart page`. The output should ask some clarifying questions and have a low clarity score.
  3. Attach the [cart image](docs/design/cart.png) to the Chat.
  4. Enter a more detailed prompt: `I want a cart Page that shows the items in the cart currently using the attached image for design elements. Match dark/light modes. Show a shipping fee of $25 but free for orders over $150. Add a cart icon to the NavBar that shows the number of items in the cart and updates when items are added/removed. When the icon is clicked, navigate to the Cart page.`
  5. You should get an even better prompt back with a high clarity score.

### Demo: Using Copilot Coding Agent to Experiment in Parallel

- **What to show:** Creating 3 variations of the Cart page in parallel.
- **Why:** Experimentation can be time-consuming and costly - unless you get Copilot Coding Agent to do it for you - in parallel! Then you can choose the option you like the best.
- **How:**  
  1. Make sure you have the GitHub Remote MCP server running
  2. Run the `/demo-cca-parallel` prompt
  3. **Note**: This takes a couple minutes to create the Issues and then Copilot Coding Agent takes about 20 minutes to complete the code changes, so be prepared for other demos or do this before your live demo and just show the results.

### Demo: Self-healing DevOps

- **What to show:** Copilot Coding Agent can self-heal failing Actions workflows.
- **Why:** Many times failing CI/CD pipelines can be fixed by simple changes - this demo shows how you can use GitHub Copilot (via the [ai-inference Action](https://github.com/actions/ai-inference)) to self-heal failing jobs.
- **How:**  
  1. You will need to generate a PAT since the prompt for analyzing the failed job uses MCP. Navigate to your GitHub Developer Settings and generate a Fine-grained token with the following permissions:
     1. Org level: `Models` read-only
     2. Repo level: `Actions` read-only, `Contents` read-only, `Issues` read/write
  2. In the repo, navigate to Settings and add a new Actions repository secret called `AUTO_REMEDIATION_PAT` with your PAT
  3. Enable the Action `Auto Analyze Build Failures`
  4. Apply the Patch Set `Copilot: Self-Healing DevOps`[^1], which will create an error in the code
  5. Commit and push
  6. This will trigger the `ci` workflow, which will fail
  7. The failure in turn triggers the `auto-analyze-failures` workflow, which will analyze the build failure, create an Issue and assign it to Copilot
  8. Copilot Coding Agent will create the PR to fix the issue by reverting the line that broke the test
  9. **Note**: This whole workflow takes a few minutes, so if you're going to show this, you may want to run this before your demo.
  10. The [failed-run-analyze.prompt.yml](.github/models/failed-run-analyze.prompt.yml) file contains the prompt used in the workflow to analyze the build failure. You can open this in the Models tab in the repo, but it requires MCP so you won't be able to test it fully in Models.

---------

[^1]: To learn how to apply a patch-set, see [patch-sets.md](../general/patch-sets.md)

## Mission Control :copilot: 🛰️

Mission Control is a user-level feature, accessible at <https://github.com/copilot/agents>, that displays all ongoing agent tasks. As it is mostly a "show and tell" feature, we've created a list of coding agents for you to kick off, followed by a [suggested (opinionated) demo flow](#opinionated-mission-control-demo).

### Prep: Agents to Kick-Off

- **Directly from Mission Control**: This is the easiest way, perfect if you want to start in Mission Control. Here are two example prompts to get you started:
  - **[Simple] Add ESLint Rule**: This is a quick one with a good impact because CCA will also fix all code to comply with the new rules.

      ```txt
      Add an ESLint rule to enforce semicolons
      ```
  
      Then you can also show how to steer Copilot mid-session:

      ```txt
      While you're at it, also add a rule to enforce double-quotes over single quotes
      ```

    After the session is done, kick off the next one with:

      ```txt
      Find a rule to disallow UPPER_CASE variables and only warn about it.
      ```

  - **[Medium] Add BDD Tests**: Select the `BDD Specialist` custom agent (see [Custom Agents](./custom-agents.md)) and use this prompt:

      ```txt
      Add BDD tests for the ToS Download feature
      ```

  - **[Complex] Fix view issue**: Copilot will chew on this for a bit, but it's great to demo mid-session how it will find the issue and use Playwright MCP to validate its fix:

      ```txt
      On the main page, for large screens, the "Smart Tech CAT" textbox does not stay within the hero image but flows out of it on the right side. Change it so that it will always be entirely within the image's boundaries, with some padding to the right of the image.
      ```

  - **From Issue(s)**: (Bulk) assign the following two issues to CCA:
  - `Improve test coverage for API`
  - `Move "Terms and conditions" to the bottom of the "Helpful Links" list`
- **CodeQL Alerts:** You have to generate autofixes before you can assign them to CCA. See [Assign CodeQL Alerts to Coding Agents](../ghas/ghas.md#assign-codeql-alerts-to-coding-agents-copilot-) below.
  - `Database query built from user-controlled sources`
  - `Workflow does not contain permissions`

### Demo: Opinionated Mission Control Walkthrough

1. **Assign Issue**: Pre-Demo: Assign the issue `Improve test coverage for API` to Copilot so you have a finished session to show.
2. **Kick-Off Agents Live**: Kick off the demo by assigning multiple tasks to the Coding Agent from the list above (don't use the ESLint one yet).

3. **Mission Control Overview**: Navigate to <https://github.com/copilot/agents>, then:
    1. Explain the purpose of Mission Control and its key features.
    2. Highlight the right side with an overview of all agents and conversations.
    3. Showcase the open sessions.
    4. Kick off a new session with the ESLint Rule from above.

    ![Mission Control overview displaying agents and conversations](../images/mission-control-overview.png)

4. **Session Overview**: Pick the running ESLint task and navigate to its session view by clicking on it.
    1. **Session Progress:** Highlight the session progress view and how it provides a live view into the Commands, MCP Servers, and other steps CCA is taking.
    2. **Overview & Files Changed Tab:** Showcase how Copilot keeps a summary in the Overview and how you can see all files changed in the Files Changed tab.
    3. **Switch Tasks:** Show how to easily switch between tasks on the left side.
    4. **Steer Copilot:**
        1. **Mid-Session:** Ask Copilot to add one more ESLint rule.
        2. **End of Session:** Trigger a new session for a completed task. Depending on whether ESLint is done, you might want to switch to the Unit-Test Tasks assigned before the demo and use this query:

          ```txt
          Improve the handling of the mock data. Use a mock-database and mock-objects.
          ```

    5. **Jump to PR or Codespace:** On the top right, you can directly jump to either the PR created by the agent or open the Codespace where the agent did its work to finalize it, concluding the Mission Control demo.

    ![Mission Control session in progress with agent activity and session details](../images/mission-control-mid-session.png)

> [!TIP]
>
> - **Add Custom Agent Demo:** You can easily combine this with the [Custom Agents](./custom-agents.md) demo by kicking off the `BDD Specialist` or `API Specialist` from the list above.
> - **Demo Flow:** In step 5, go to the PR and jump directly into the [Copilot Code Review](./copilot-code-review.md) Demo.
> - **Run at least one coding agent before the Demo**: The coding agent takes a while and might not finish in time for your demo. This way, you'll have a finished session.
> - **Preselect the Demo Repo in Mission Control:** Have a tab open with Mission Control, where you've already selected your demo repository as the target. The repo search is currently a bit tedious and slow—this way, you don't lose valuable demo time searching for your demo repo.
> - **Kick off multiple agents:** Pick a few (or all) from the list above, the ones you feel most comfortable demoing. Mission Control is all about managing multiple agents, so this is what you should show.

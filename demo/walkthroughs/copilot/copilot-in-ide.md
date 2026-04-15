# Copilot in the IDE

> Note: For the most basic "What can Copilot do?" scenario, use the `demo-unit-test-coverage` prompt to have Agent Mode add some unit tests.

## General Copilot Features

### Demo: Enhancing Unit Tests and Coverage

#### Option 1: Using Coding Agent

If you want to demo Copilot Coding Agent, there is an Issue for improving Code Coverage on the repo - it should be Issue #2 (`Improve test coverage for API` - created as part of the demo spinup). Assign this to Copilot - that's it. This takes about 15 mins, so do this ahead of time if necessary!

> [!TIP]
> **Using 3rd Party Agents:** When assigning an issue to Copilot, you can open the **Web agents panel** or use the **Agent Picker** in VSCode to select an alternative agent such as **Claude** or **Codex** instead of the default Copilot Coding Agent. This allows you to leverage different agents for your coding tasks.

#### Option 2: Live Coding

- **What to show:** Copilot generating multiple tests, executing them, analyzing coverage and self-healing, plus demonstrate efficient use of custom prompts for testing workflows.
- **Why:** Show Copilot's ability to quickly and easily generate tests, validate them, self-heal and analyze coverage. Also demonstrate how custom prompts can standardize testing practices.
- **Approach 1 - Custom Prompt (Recommended for demos):**
  1. Open the [demo-unit-test-coverage.prompt.md](../../.github/prompts/demo-unit-test-coverage.prompt.md) file
  2. Show the prompt structure: pre-configured for Agent mode, comprehensive tool list, detailed testing requirements
  3. Explain how it includes specific coverage requirements, CRUD operations, error handling, etc.
  4. Click "Run" to execute the automated test generation
  5. Show how it creates comprehensive test files for both Product and Supplier routes
  6. Demonstrate the self-healing capabilities when tests fail

- **Approach 2 - Manual Chat (For deeper explanation):**
  1. Ask Copilot to `run tests, analyze coverage and add missing Branch tests to include tests for untested scenarios`
  2. Show Agent working on the tests and adding new tests for the API Branch route
  3. Show Copilot "self-healing" (if tests fail)
  4. Accept the changes
  5. Ask Copilot to `add tests for the Product route` to show generation of new tests

- **Key Takeaway**: Custom prompts can encapsulate testing best practices and ensure comprehensive coverage automatically.

### Demo: Using Vision and Agent to Generate Cart Functionality

> [!NOTE]
> **Quick Start Option**: Use the `demo-cart-page.prompt.md` custom prompt for an automated demo. This prompt will have Agent Mode implement the complete Cart Page functionality automatically with proper context and tools pre-configured.

- **What to show:** "Vibe coding" using Agent Mode and Vision to complete complex tasks, plus demonstrate custom prompt efficiency.
- **Why:** Demonstrate how Copilot Vision can detect design patterns, how Agent can understand a codebase and create complex changes over multiple files, and how custom prompts can streamline complex demos.

#### Approach 1 - Custom Prompt (Recommended for demos)

  1. Open the [demo-cart-page.prompt.md](../../.github/prompts/demo-cart-page.prompt.md) file
  2. Show the prompt structure: comprehensive tool list, detailed context about the current state
  3. Attach the [cart image](../docs/design/cart.png) to the prompt
  4. Click "Run" to execute the entire cart implementation automatically
  5. Show how the custom prompt handles the complete workflow with proper context

#### Approach 2 - Manual Chat (For deeper explanation)

  1. Run the App to show the original code. Once the site starts, click on "Products" in the NavBar and show the Product Page. Add an item to the Cart - note that nothing actually happens, except a message saying, "Added to Cart". Explain that there is no Cart in the frontend app currently.
  2. Open Copilot and switch to "Plan" mode.
  3. Attach the [cart image](../docs/design/cart.png) using the paperclip icon or drag/drop to add it to the chat.
  4. Enter the following prompt:

    ```txt
    I need to implement a simple Cart Page. I also want a Cart icon in the NavBar that shows the number of items in the Cart.
    ```

  5. Highlight that Copilot has suggested changes and planned the components to add/modify.
  6. (OPTIONAL if you have the GitHub MCP Server configured): Ask Copilot to `create an issue in my repo to implement the Cart page and Cart icon`
  7. Show the issue in the repo
  8. Switch to "Agent" mode in Copilot Chat. Switch to `Claude 3.5 Sonnet` (a good implementation model) and enter this prompt:

    ```txt
    Implement the changes.
    ```

  9. Show Copilot's changes and how you can see each one and Keep/reject each one.
  10. Accept Copilot's suggested fixes.
  11. Go back to the Frontend app. Navigate to Products. Show adding items to the cart (note the icon updating). Click on the Cart icon to navigate to the Cart page. Show the total, and adding/removing items from the cart.

- **Key Takeaway**: Custom prompts provide consistency and can encapsulate complex workflows that would otherwise require multiple manual steps.

## Customizations

### Demo: Custom Prompt Files and standardized AI-Workflows

- **What to show:** Reusing custom prompts to streamline AI-native workflows and demonstrate prompt engineering best practices
- **Why:** Demonstrate how Copilot and VSCode use custom prompts to help streamline AI-native workflows, keep developers in the flow, and provide consistent, repeatable results.
- **How:**  
  1. **Model Selection Prompt**: Show the [model-selection.prompt.md](../../.github/prompts/model-selection.prompt.md) file in the prompts directory. Explain the YAML frontmatter (mode: 'agent', description, tools). Click the Run button in the top (or use Command Palette → "Prompts: Run Prompt") and show how it automatically selects Agent mode, fetches live documentation, and helps choose the best model or updates the comparison markdown file.
  2. **Quick Demo Prompts**: Show the available demo prompts in the `.github/prompts/` directory:
     - `demo-cart-page.prompt.md` - Complete cart implementation with vision
     - `demo-unit-test-coverage.prompt.md` - Automated test generation and coverage analysis
  3. **Custom Chat Modes**: Show `Plan`, `ModelSelection` and `BDD` modes - each outlined below.
  4. **Live Demo**: Run one of the demo prompts (e.g., `demo-unit-test-coverage.prompt.md`) to show Agent mode automatically executing a complex workflow.
  5. **Note:** Explain that custom prompts provide consistency, reduce cognitive load, and can be shared across teams for standardized workflows.

### Demo: Custom Instructions and Repository Configuration

- **What to show:** Copilot's **Custom Instructions** feature using the existing `.github/copilot-instructions.md` configuration.
- **Why:** Demonstrate that Copilot can be customized and personalized for internal libraries, coding standards, and team practices that don't exist in the foundational models.
- **How:**  
  1. Show the existing [.github/copilot-instructions.md](../../.github/copilot-instructions.md) file in the repository
  2. Explain how this file provides context about:
     - Repository information (owner, repo name)
     - Architecture references
     - Build and testing instructions
  3. **Demo Enhanced Custom Instructions**:
      1. Option 1: Apply the Patch Set `Copilot: Custom Instructions`[^1] which will update the custom-insturctions file
      2. Option 2: Update the custom instructions file by hand, adding these additional guidelines

          ```markdown
          ## Additional Guidelines for REST APIs
          
          For REST APIs, use the following guidelines:
          
          * Use descriptive naming
          * Add Swagger docs for all API methods
          * Implement logging and monitoring using [TAO](../docs/tao.md)
            - assume TAO is installed and never add the package
          ```

  4. Show the [TAO](./tao.md) documentation to demonstrate the fictional internal library
  5. Ask Copilot to `add observability to the Supplier route using our internal standards`
  6. Show how Copilot uses the custom instructions to implement TAO observability patterns
  7. **Note**: Explain that this will not compile since TAO doesn't really exist - this demonstrates how custom instructions can reference internal frameworks
  8. **Key Takeaway**: Custom instructions allow teams to encode their specific practices, internal libraries, and coding standards

### Demo: Review files for compliance with 3p Web Interface Guidelines using agent skills

- **What to show:** Copilot's ability to review code for specific web accessibility guidelines using the Web Interface Guidelines agent skill
- **Why:** Demonstrate that Copilot can be used to enforce best practices and guidelines, such as web accessibility standards using 3p [agent skills](https://agentskills.io/home), an open standard for custom instructions
- **How:**  
  1. In the terminal run the following command `npx skills add vercel-labs/agent-skills --skill web-design-guidelines -a github-copilot` to add the Web Interface Guidelines skill to the repository.
  2. Open the `SKILL.md` and explain that this skill provides guidelines for accessibility, typography and other web design best practices. It fetches the latest guidelines from GitHub. For all guidelines see the [Web Interface Guidelines Documentation](https://raw.githubusercontent.com/vercel-labs/web-interface-guidelines/main/command.md)
  3. Ask Copilot to `Review the UI`
  4. Show how Copilot reads the skill, fetches the guidelines and reviews the code 
  5. Once review is complete, share some findings and optionally ask Copilot to fix some issues
  6. **Key Takeaway**: Copilot supports 3p agent skills to customize Copilot Chat 

## Security

### Demo: Copilot and Application Security

- **What to show:** Copilot's ability to understand and remediate security vulnerabilities
- **Why:** Demonstrate that Copilot can be used to scale AppSec by bringing security expertise to Developers directly.
- **How:**  
  1. Open Copilot Chat and switch to `Ask` mode.
  2. Ask Copilot to `analyze @workspace and check if there are obvious security vulnerabilities`
  3. You should see issues like:
      - Cross-site Scripting (XSS) vulnerability
      - Command Injection Vulnerability
      - Insecure CORS Configuration
      - Missing Security Headers
      - Insecure Authentication Implementation
  4. Chat with Copilot to address one of these issues: `generate a fix for ...`
  5. (Optional with GitHub MCP Server): Ask Copilot to `create an issue to fix ...` and select a vulnerability for Copilot to create an Issue

## CI/CD

### Demo: Automating Deployment with GitHub Actions, Azure and Bicep

- **What to show:** Copilot generating Actions workflows and Infrastructure-as-code.
- **Why:** Show Copilot's ability to automate CI/CD workflows.
- **How:**
  1. Ensure that you have run the [configure-deployment.sh](../infra/configure-deployment.sh) script to set up the initial infrastructure and configure the environments and vars in the repo.
  2. Add the [deployment.md](../docs/deployment.md) file as context.
  3. Prompt Copilot Agent to `generate bicep files and workflows according to the deployment plan`
  4. Show generated files:
     - GitHub Actions YAML to build & test
     - GitHub Actions YAML to deploy including an approval step
  5. Accept the changes
  6. Commit and push to see the pipeline execution
  7. Show the deployment

## TDD Agent Mode

### Demo: TDD Workflow with Agent Handoffs

- **What to show:** Complete Test-Driven Development workflow using specialized agent modes with automatic handoffs between planning, test writing, and implementation phases.
- **Why:** Demonstrate how custom agent modes can orchestrate complex multi-phase workflows, maintain state between phases, and enforce software engineering best practices like TDD automatically.
- **How:**

  1. **Planning Phase:**
     - Open Copilot Chat and select `tdd-planner` from the Chat Modes dropdown
     - Enter the prompt: `I want a new method that gets the orders for a branch ID`
     - Show how the planner agent:
       - Researches the codebase to understand existing patterns
       - Creates a comprehensive TDD plan document in `docs/tdd-plans/`
       - Defines test specifications with Given-When-Then format
       - Documents acceptance criteria and edge cases
       - Identifies files to create/modify
     - Review the generated markdown file showing the complete plan

  2. **Red Phase (Failing Tests):**
     - Click the **"Write Red Tests"** button that appears in the plan document
     - This automatically switches to `tdd-red` mode and hands off the plan
     - Show how the Red agent:
       - Reads the TDD plan for test specifications
       - Creates comprehensive test files following the plan
       - Runs tests to verify they fail correctly (expected behavior)
       - Reports test failure reasons
     - Review the generated test file (e.g., `api/src/routes/order.test.ts`)
     - Show the test output confirming all tests fail with HTTP 404 (route not implemented)

  3. **Green Phase (Minimal Implementation):**
     - Click the **"Write Green Implementation"** button from the test results
     - This automatically switches to `tdd-green` mode and hands off context
     - Show how the Green agent:
       - Analyzes failing tests to understand requirements
       - Implements minimal code to make tests pass
       - Adds appropriate Swagger documentation
       - Runs tests to verify they now pass
       - Reports success with implementation summary
     - Review the implementation (e.g., new route in `api/src/routes/order.ts`)
     - Show all tests passing with green checkmarks ✅

  4. **Key Points to Highlight:**
     - **Automatic Context Handoffs**: Each phase automatically passes the right context to the next
     - **Enforced Best Practices**: TDD workflow is enforced by the agent modes
     - **Minimal Implementation**: Green agent only writes code required by tests (no over-engineering)
     - **Self-Verification**: Each phase validates its work (tests run automatically)
     - **Documentation**: Complete audit trail in the TDD plan document
     - **Code Quality**: Follows existing patterns and conventions automatically

  5. **(Optional) Show the Agent Configuration:**
     - Open `.github/copilot-modes.json` to show the three TDD agents:
       - `tdd-planner`: Creates comprehensive test plans with research
       - `tdd-red`: Writes failing tests following the plan
       - `tdd-green`: Implements minimal code to pass tests
     - Highlight how each mode has specific instructions and constraints
     - Show how modes can be chained together for complete workflows

- **Key Takeaway**: Custom agent modes enable sophisticated, multi-phase workflows that enforce best practices while maintaining context across handoffs. This creates a consistent, repeatable process that teams can rely on for quality software delivery.

## Planning Mode in VSCode :copilot: 📝

1. Open the demo repository in **VSCode**.
2. Open Copilot Chat and switch to `Plan` Mode.
  
    ![Switching to Plan Mode in Copilot Chat](../images/vscode-plan-switch-mode.png)

3. Use the following prompt to kick off a planning session:

    ```txt
    I need to implement a cart feature in this application. Help me plan that.
    ```

4. Copilot will first retrieve some information and then come back to you with some questions (CAUTION: They might not be exactly the same as below, so just get creative):

    ![Planning Mode questions in Copilot Chat](../images/vscode-plan-questions.png)

5. Answer the questions accordingly. Copilot might come back with more questions. You can decide whether to keep answering them or, at some point, turn the plan into action.

6. Choose one of the following actions:
    ![Planning Mode actions in Copilot Chat](../images/vscode-plan-action-choices.png)
    1. Click `Start Implementation` to have Agent Mode implement the plan right away.
    2. Click `Open in Editor` to store the plan in a markdown file.
    3. Switch to `Agent` Mode and prompt Copilot to hand this over to the Coding Agent (requires MCP to be started):

        ```txt
        Can you hand this plan over to coding agent?
        ```

---------

[^1]: To learn how to apply a patch-set, see [patch-sets.md](../general/patch-sets.md)

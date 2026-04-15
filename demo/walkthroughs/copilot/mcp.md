# MCP Servers

## **MCP Server install and config**

If you are wanting to show MCP server integration, you will need to set up and configure the MCP servers _prior_ to the demo. I have included the necessary `mcp` config in the [mcp.json](../.vscode/mcp.json) file. Open the file and use the HUD display above the servers to start them:

![VS Code MCP server configuration showing playwright and github servers with Start buttons](./mcp.png)

You can also use the Command Palette to start the MCP servers.

> [!NOTE]
> There are 2 GitHub MCP server: `github-local` and `github-remote`. The local server runs off `docker` (you may want to change this to `podman` if you have Podman installed). This will prompt for a PAT. The remote server connects to the Remote MCP server and uses OAuth to authenticate. **Start one or the other, not both**!

### Start the Playwright MCP Server

- Use the cmd palette `Cmd/Ctrl + Shift + P` -> `MCP: List servers` -> `playwright` -> `Start server`

### Start the GitHub MCP Server

> [!IMPORTANT]
> Generate a fine-grained PAT that has permissions to read/write Issues and PRs, context and whatever other features you want to demo. You can create this at the org/repo level. I suggest creating a PAT and storing it in a key vault (or 1Password) so that you have it handy.

- This server runs via Docker image, so you will need Docker to be installed and running before starting this server. I use Podman on my Mac.
- Use the cmd palette `Cmd/Ctrl + Shift + P` -> `MCP: List servers` -> `github` -> `Start server`. The first time you run this, you will have to supply a PAT.

> **Pro tip:** If you want to change the PAT, open the Settings json file. You will see `"id": "github_token" = ****` in the `input` section. Right-click on the `***` section to edit or clear the cached token. (The `***` is a GUI feature - the value is not actually stored in the json file)

## Demo: MCP Servers - Playwright  

- **What to show:** Launch browser navigation using Playwright MCP server to show functional testing from natural language, plus demonstrate feature file generation with custom prompts.
- **Why:** Demonstrate support for extending Copilot capabilities using MCP server protocol and how custom prompts can standardize testing practices.
- **Part 1 - Custom BDD Mode:**
  1. Switch to `BDD` mode.
  2. Run the prompt `add a feature to test the cart icon and page` to generate comprehensive Gherkin feature files for Cart functionality
  3. Show the generated behavioral test scenarios

- **(Optional) Part 2 - Playwright MCP:**
  1. Ask Copilot to `browse to http://localhost:5137 and execute the test steps`
  2. Accept the Playwright command requests and show Copilot "running" the test.
  3. (Optional): Ask Copilot `to generate headless Playwright tests for the .feature file`

- **Key Takeaway**: MCP servers extend Copilot's capabilities while custom prompts can standardize testing approaches across teams.

## Demo: MCP Servers - GitHub

- **What to show:** Interact with GitHub from Chat.
- **Why:** Demonstrate support for extending Copilot capabilities using MCP server protocol as well as the GitHub MCP server.
- **How:**  
  1. Switch to Agent mode
  2. Ask Copilot to `check which issues are assigned to me in the repo`.
  3. Show how Copilot fetched issues (or shows there are no issues)
  4. Ask Copilot to `create an Issue for enhancing test coverage in the API project and assign it to me`. (Don't forget to check the owner/repo in the args!)
  5. Show how Copilot creates a new Issue with a meaningful description and labels
  6. (Optional): Assign the issue to Copilot to queue off Copilot Coding Agent!

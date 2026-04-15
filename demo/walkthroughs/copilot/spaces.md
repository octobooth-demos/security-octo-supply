# Copilot Spaces

- [Copilot Spaces](#copilot-spaces)
  - [Demo: Compliance Space Demos](#demo-compliance-space-demos)
    - [Compliance Space Demo 1: General Copilot Spaces Questions](#compliance-space-demo-1-general-copilot-spaces-questions)
    - [Compliance Space Demo 2: Combine a space and codebase for compliance assessment](#compliance-space-demo-2-combine-a-space-and-codebase-for-compliance-assessment)
    - [Compliance Space Demo 3: Use Spaces MCP to check specific code for compliance](#compliance-space-demo-3-use-spaces-mcp-to-check-specific-code-for-compliance)
      - [Enabling Spaces Tools in MCP](#enabling-spaces-tools-in-mcp)

## Demo: Compliance Space Demos

The Copilot Spaces demo revolves around the `OD OctoCAT Supply Compliance Docs` space, which is fed by a repository of the same name: `od-octocat-supply-compliance-docs`. You can find both in the organization of your demo repository.

> [!IMPORTANT]
> **Do not change the Space or Repo!:** Both are static resources shared by all demos in your demo environment. Please do not change or modify them, as this will impact other demos.

The space and its documents are designed to be a realistic example of the legal, security, and compliance-related rules and guidelines that a software development team in an enterprise must follow.

There are several demos included that you can perform individually or in the sequence presented here.

> [!NOTE]
> **A note on model choice:** The choice of model makes a significant difference in the structure and length of the answers:
>
> - `GPT-4` is extremely chatty and goes into detail. This is good if the purpose of the prompt is to feed information back to an AI assistant (e.g., for issue generation or coding) to provide all relevant context, but it's less ideal for a human to read through.
> - `Gemini 1.5 Pro` on the other hand is much more concise and to the point, which is often preferable for humans.
> - `Claude 3 Sonnet` is somewhere in between, but also tends to be more concise.
>
> Feel free to experiment with other models as well.

### Compliance Space Demo 1: General Copilot Spaces Questions

1. Navigate to `/copilot/spaces` and, in the Organization Tab, find the `OD OctoCAT Supply Compliance Docs` space.
2. Highlight the sophisticated instructions used to tune Copilot's behavior for compliance questions. You can also show the contents and repository, making it clear that compliance docs are often long and complex.
3. Ask the following question (the `Gemini 1.5 Pro` model is recommended):

    ```txt
    I need to implement a cookie banner. What do I need to keep in mind from a compliance perspective?
    ```

4. Showcase how Copilot is able to extract relevant information from the compliance docs and provide a comprehensive answer, as well as cite its sources for further investigation.

### Compliance Space Demo 2: Combine a space and codebase for compliance assessment

1. Navigate to `/copilot/` (Note: not `/copilot/spaces`).
2. Attach both the Copilot Space `OD OctoCAT Supply Compliance Docs` and your demo repository as additional context.
3. Prompt using `GPT-4` (very chatty) or `Gemini 1.5 Pro` (more concise):

    ```txt
    Please assess my repository's readiness in terms of compliance before we can ship it.
    What gaps are still there that we need to fill before we can release our webshop?

    Be thorough, please analyze the existing PRs and Issues to see what we've already covered - and list what we still need to implement.
    ```

4. Showcase how it will yield a comprehensive compliance assessment of the codebase, referencing specific files and PRs, while also generating a list of things that need to be done to make the codebase compliant before general availability (GA).
5. Prompt again:

    ```txt
    Okay, can you help me group all those required steps a bit better and make it more concise?

    Generate a list of actionable items to tackle, group them into phases, and prioritize them. What is a blocker for GA (Phase 0), what is required soon after GA (Phase 1), and what can we leave for later (Phase 2)?
    ```

6. Showcase how Copilot will again use the space's information to prioritize and group the compliance tasks into actionable phases.
7. Lastly, prompt it to draft a sophisticated list of parent and sub-issues for all these tasks:

    ```txt
    Now please generate issues for me.

    Create one parent issue as an Epic titled "Become Compliant", then add all the other tasks as sub-issues with all the details. Mark them clearly for the different phases.
    ```

8. It is up to you to generate those issues and then start assigning them to Copilot Coding Agent for implementation.

### Compliance Space Demo 3: Use Spaces MCP to check specific code for compliance

> [!IMPORTANT]
> Only the GitHub Remote MCP currently has the Copilot Spaces tools available. Also, as they are not part of the default toolset, you'll have to specifically configure them. If you don't have it working, follow the [instructions below](#enabling-spaces-tools-in-mcp). In the delivered Codespace, this is already configured as `github-remote`.

Demo walkthrough steps:

1. Check out the repository and ensure you have the `github-remote` MCP server running.
2. Check out the `feature-add-tos-download` branch.
3. Open Copilot Chat and switch to `Agent` mode, using the `GPT-4` or `Gemini 1.5 Pro` model.
4. Enter the following prompt:

    ```txt
    Get the contents of the Copilot Space `OD OctoCAT Supply Compliance Docs`. Once you have those, please analyze my current changes in the PR: Did we include all the necessary languages for the Terms of Service download?
    ```

5. Additional prompts at your disposal:

    ```txt
    Check if we have all the necessary legal disclaimers included in our Privacy Policy update.
    ```

    ```txt
    We need to implement a Cookie Banner. Implement it according to the compliance requirements we have in our Copilot Space `OD OctoCAT Supply Compliance Docs`.
    ```

#### Enabling Spaces Tools in MCP

The Copilot Spaces Tools are **not** enabled in the default toolset of the GitHub Remote MCP Server. To enable them, you have to use the `X-MCP-Toolsets: copilot_spaces` header in your `.vscode/mcp.json` file. As you might want to combine it with other tools, here is an example config:

```json
"github-remote": {
  "type": "http",
  "url": "https://api.githubcopilot.com/mcp/",
  "headers": {
    "X-MCP-Toolsets": "actions, code_security, dependabot, discussions, issues, orgs, projects, pull_requests, repos, secret_protection, security_advisories, copilot, copilot_spaces"
  }
},
```

You could also just configure a dedicated MCP server entry for Copilot Spaces only, if you want to keep things separate:

```json
"github-copilot-spaces": {
  "type": "http",
  "url": "https://api.githubcopilot.com/mcp/",
  "headers": {
    "X-MCP-Toolsets": "copilot_spaces"
  }
},
```

For a full documentation, refer to the [github/github-mcp-server documentation](https://github.com/github/github-mcp-server/blob/main/docs/remote-server.md?tab=readme-ov-file#additional-remote-server-toolsets).

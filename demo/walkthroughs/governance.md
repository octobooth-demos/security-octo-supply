# Governance Features

## Rulesets & Custom Properties

The repository has an organization ruleset [OD OctoCAT Supply Ruleset](https://github.com/organizations/rsa-2026/settings/rules/13759533) applied, setting up a few of the standard rules like:

1. Protecting the main branch from deletions, force pushes etc.
2. Requiring a pull-request
3. Requiring a CodeQL Scan
4. Requiring the required workflow [Dependency Review]()

The ruleset is applied through the custom property `od_octocat_supply_risk_tier` with a value of `high`. This custom property is also applied to the demo repositories.

You can use this to showcase how rulesets can be applied dynamically based on metadata on a repository - and how this gives both control and flexibility.

In the demo, there will be a pre-created pr `Feature: Add ToS Download` where some of the rules will be violated. You can demo the effect of rulesets on this PR by showing how the violations are flagged and what the implications are for the development process.

## Copilot Control Plane: "AI Controls" :copilot:🎛

![Screenshot showing the location of the new AI Controls page](./images/copilot-control-plane-navigation.png)

The new AI Control Plane is an enterprise-level settings page that unifies the monitoring and management of AI-related features across an organization. It combines existing features, like policy and access control, with new capabilities such as Agent Rulesets.

> [!NOTE]
> You can only demo this if you have access to an enterprise administration account (`https://github.com/enterprises/<your-enterprise>`).

> [!IMPORTANT]
> **READ-ONLY DEMO**: Given that changing anything in the enterprise settings can be severely disruptive to all organizations and users, please refrain from demoing any configuration changes in this section. This is a read-only demo.

### 1. Agents Section

This section is the landing page and provides a comprehensive overview of agent-related activities and configurations.

For demo purposes, we recommend grouping this into `GitHub Agents` and `Custom Agents` for better clarity.

#### 1.1. GitHub Agents: Installed Agents, Agent Sessions, and Audit Logs

> [!TIP]
> Ideally, you have kicked off your own CCA task before coming here so you can drill down on it in this part.

1. **Installed Agents:**
    1. Navigate to the **Installed Agents** view.
    2. Show that both `Copilot code review` and `Copilot coding agent` are installed for the enterprise.
    3. Explain that this is where additional GitHub Agents will appear in the future.
2. **Agent Sessions:**
    1. Navigate to the **Agent Sessions** view.
    2. Show that sessions can be filtered by agent type (`Code Review` or `Coding Agent`) and by specific organizations.
    3. Point out a running session that was kicked off from an issue assignment.

        > [!WARNING]
        > Right now, trying to access anyone's session but your own will lead to a 404 error. This will be fixed in the future.
3. **Copilot Audit Log:**
    1. Finally, navigate to the **Audit Log** via the redirect link.
    2. Show that the log is pre-filtered with `actor:Copilot` to show only AI-related activities.

        ![AI Control Plane Audit Log filtered by actor:Copilot](./images/copilot-control-plane-actor-filter.png)

    3. Find and highlight an `agent_session.task` event.
    4. Click on an event and inspect the details to show the new agent-specific fields:
      - `actor_is_agent: true`
      - The `user` field, which shows who the agent is acting on behalf of.

        ![Agent Session Details](./images/copilot-control-plane-audit-log.png)

#### 1.2. Custom Agents: Configuration and Rulesets

> [!NOTE]
> This feature will become a multi-select field in the future, so enterprises can select custom agents from more than just one organization.

This area allows you to standardize custom agents across your enterprise.

1. **Setting Up Custom Agents:**
    1. Depending on your enterprise, you will see that the demo organization is pre-selected. Highlight that, in the future, this will be a multi-select field.
    2. Click on one of the custom agents listed to show the implementation and highlight the `.github-private` repository and its structure, which is the same for every custom agent.
    ![Custom Agent implementation in a private repository](./images/copilot-control-plane-custom-agents.png)
2. **Controlling Custom Agent Generation:**
    1. You will see an existing rule for custom agents—click it.
    ![Screenshot of the existing custom agent ruleset in evaluation mode](./images/copilot-control-plane-custom-ruleset.png)
    2. Explain that if the rule doesn't exist, it will be created on the first click.
    3. Explain that, by default, this predefined ruleset prevents any member from creating a new custom agent on a supported file path (`.github/agents/*.md`) for all organizations and repositories.
    4. Explain that an enterprise owner can relax this setting as they see fit, from allowing only certain organizations or even repositories to disabling it entirely for full freedom.

> [!TIP]
> We have the rule in `Evaluate` mode for our own demo purposes (we want to allow demoing custom agents across the enterprise). This is still a good example of how the rule can be relaxed.

### 2. Copilot Section

**Nothing new to see here:** The Copilot Section is just a new home for the already existing Copilot policies. Nothing has changed in those policies, so there is not much to show, but you can mention that it puts Copilot-related settings into a unified place.

![Screenshot showing the previous copilot policies page, which just has a redirect to the new home under "AI Controls" now](./images/copilot-control-plane-policies.png)

### 3. MCP Section

> [!IMPORTANT]
> Today, the allowlist is only enforced in VS Code. Additional IDEs and GitHub.com support are coming soon.

1. Navigate to the **MCP** section.
2. Highlight the new policy for setting an **MCP allowlist** via a third-party MCP registry.
3. Explain that when you configure a third-party registry URL here, it creates an enterprise-wide allowlist. This ensures that only validated MCP connections are permitted when developers use agents in VS Code.

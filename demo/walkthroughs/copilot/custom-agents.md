# Custom Agents :copilot: 🤖

Custom Agents are a new feature that allows you to create your own agents with custom prompts and toolsets. They reside in the `.github/agents` folder of your repository, or can now also be defined centrally for entire enterprises as seen in the [Custom Agents Configuration and Rulesets Walkthrough](../governance.md#12-custom-agents-configuration-and-rulesets).

> [!IMPORTANT]
> VSCode's `ChatModes` are becoming `Agents`. The functionality is the same, but the naming has changed to better reflect their purpose.

## BDD Agent

1. Navigate to the demo repository.
2. Open the `.github/agents/bdd-specialist.md` file and showcase it to your customers.
3. On the top right, click the Agents Panel.
    ![Agents Panel button in the repository view](../images/custom-agent-agents-panel.png)  
4. Select the `feature-add-tos-download` branch.
    ![Branch selection dropdown in the Agents Panel](../images/custom-agent-select-branch.png)

5. Select the `BDD Specialist` agent.

    ![Selecting the BDD Specialist agent in the Agents Panel](../images/custom-agent-select-agent.png)

6. Type the following prompt and start the session:

    ```txt
    Add comprehensive BDD Tests to the ToS Download feature!
    ```

    ![Starting a new session with the BDD Specialist agent](../images/custom-agent-select-prompt.png)

7. Go to Mission Control to showcase the running session (see [Mission Control](./copilot-coding-agent.md#mission-control-copilot-) for details on how to demo it).
8. During or after the session, highlight how the `BDD Specialist` agent can be used with little to no prompting and still come up with a sophisticated BDD test suite.

> [!TIP]
> In the list of custom agents, you will see the `Documentation Specialist` and the `Compliance Bot` - these are custom agents defined on the enterprise level - you can hint at them, but defer more explanation once you reach the [Custom Agents Configuration and Rulesets](../governance.md#12-custom-agents-configuration-and-rulesets) demo.

### Alternative 1: Use Cart Feature

Alternatively, you can use the `add-cart-page` branch to demo this feature with the prompt:

```txt
Add comprehensive BDD Tests to the Cart Page feature!
```

## Alternative 2: Use `API Specialist`

Follow the same steps as above, but use the `API Specialist` located in `.github/agents/api-specialist.md` and the prompt:

```txt
Implement a new Cart API endpoint with a `POST /cart` request that returns a cartId, and CRUD endpoints where that API can be used as `/cart/:cartId`.
```

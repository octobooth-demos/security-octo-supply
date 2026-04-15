# Copilot Code Review :copilot: 👀

> [!WARNING]
> The new CCR features are still behind a feature flag and will only be available after the Keynote on Tuesday.

Copilot Code Review (CCR) now comes with additional capabilities:

- **Better context awareness** by accessing the Code Graph
- **Additional tool calling** with CodeQL and ESLint as the first supported tools
- **Execution in Actions** for better visibility and auditability
- **The ability to hand off suggestions to a Coding Agent** for quicker implementation, even for more sophisticated fixes

## Copilot Code Review Demo Flow

1. **Assign CCR to PR:** In the demo repository, find the PR `Feature: Add ToS Download` and assign Copilot as a reviewer.
2. **Navigate to Actions:** CCR will now execute within GitHub Actions. You can find it in `Actions` → `Copilot Code Review`.

    ![Copilot Code Review workflow in Actions](../images/ccr_workflow.png)

   1. **CodeQL Analysis:** Highlight how CCR now uses a CodeQL Scan out of the box.

      ![CodeQL analysis step in Copilot Code Review Actions](../images/ccr_action.png)

   2. **Autovalidated (for ESLint):** Navigate to the `Autovalidate` job. In there, you'll find a step called `Run ESLint`. Use this to highlight how CCR can now run ESLint as part of its review.

      ![Autovalidate job showing the Run ESLint step](../images/ccr_eslint.png)

3. **Review Findings:** Once the review is done, go back to the PR. Now, here is the tricky part: Given Copilot's non-deterministic behavior, the findings might differ from demo to demo. If there isn't a matching finding, either try re-assigning Copilot again or just talk through the findings customers can additionally expect:

   1. **[CodeQL] Found Vulnerability:** Thanks to the CodeQL scan, CCR found a path traversal vulnerability in the new endpoint.

      ![CodeQL finding showing a path traversal vulnerability in the new endpoint](../images/ccr_finding_codeql.png)

   2. **[ESLint] Rule Violation:** CCR found an issue thanks to the ESLint run that yields an error (this is also found by our CI Workflow, but with CCR, we get an easy fix).

      ![ESLint finding showing a rule violation in the new endpoint](../images/ccr_finding_eslint.png)

   3. **[Instructions] Missing Swagger:** Show how CCR found missing Swagger documentation for the new endpoint. This finding stems from the instructions file in `.github/instructions/api.instructions.md`. You can open the file to show how CCR digests instructions files to improve its review.

      ![CCR finding showing missing Swagger documentation from the instructions file](../images/ccr_finding_instructions.png)

   4. **Additional Context:** CCR uses the Code Graph to find additional context that is **not** part of the PR. This is an example of that: The PR does not contain anything about React Query.

      ![CCR finding showing additional context from the Code Graph that is not part of the PR](../images/ccr_finding_additional_context.png)

4. **`Implement Suggestions`:** Lastly, explain how we can hand over the implementation of all findings to CCA at the click of a button.

    ![Implement Suggestions button in a pull request for Copilot Code Review](../images/ccr_handover_to_cca.png)

## Copilot Group Changes in PRs :copilot: 📦

> [!WARNING]
> Group Changes will only appear in a PR created or edited by a human, which is why it won't appear in the existing `Feature: Add ToS Download` PR. We've added an additional branch, `feature-add-cart-page`, for you to easily demo this feature.

1. Open a pull request from the `feature-add-cart-page` branch to the `main` branch (it should prompt you when you access the demo repo for the first time).
2. Navigate to the `Files changed` tab of the PR.
3. On the top right, showcase how Copilot grouped changes into logical sections, making it easier to review and understand the modifications.

  ![Grouped changes in a pull request](../images/copilot-group-changes.png)

> [!TIP]
> You can alternatively make a fake commit to the existing `Feature: Add ToS Download` PR (add a comment somewhere) to trigger group changes. You can also combine this with the [Code Quality Demo](#-code-quality-demo), which requires this step anyway.

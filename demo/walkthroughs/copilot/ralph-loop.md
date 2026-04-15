# Ralph Loop Demo

A "Ralph loop" is an iterative development pattern that uses AI to continuously work on a task until it is completed. Iterations are fresh to keep from poluting the context window and give the impression of "infinite context".

You can get the same effect as a Ralph loop using custom agents and subagents!

**Key benefits of "Ralph Loop" in VSCode:**
- no need to set up offline agents or compute to run them
- you are still in complete control and can stop the loops any time you want to
- you can continue in the background or hand off to Cloud if you need to
- Copilot is unlikely to create a new AI religion (unless you tell it to :-) )

## Demo: Using the `api-endpoint` Skill to Add a New Entity

In this scenario, we are going to use a "API Test Coverage" loop. The [looper agent](.github/agents/api-coverage-looper.agent.md) will search the API routes and determine which routes have tests and which don't. It will then use the `subAgents` tool to invoke the [test-writer agent](.github/agents/api-test-writer.agent.md) in parallel, (each as a subagent to keep context tight) to write tests for the route, using the `argument-hint` to pass in the route to write tests for.

Once the loop starts, you can then start a new session and the looper will continue in the background until all the routes have tests!

### How

1. Open a new Copilot Chat session.
2. Select "Local" and then `API Coverage Looper` as the agent. Select a model - a small model like Haiku or Gemini Flash should work just fine for this demo.
3. Type `complete test coverage` in the Chat and let it go.
4. Let it go for a few seconds and point out that a set of parallel subagents has started working each with their own thread to keep context clean.
5. You'll have to click "Allow tools" for each subagent (the easiest is to "Allow all tools in this session") otherwise every tool invocation will prompt for permission.
6. Point out that this is `yolo` and that you should be careful doing this!
7. At this point you can click the top-left "Back" arrow to start a new Chat - this work continues on the original session which you can switch back to in the session view.
8. Once the subagents complete, you should see a final report showing coverage for all the API routes.

## BONUS! Copilot CLI Fleet Demo

As an alternative, you can use CLI's `fleet` command to run the subagents in parallel!

Install the latest version of Copilot CLI. Then `copilot` into the repo folder.

1. Type `yolo` to allow all commands (pointing out the risks of doing this!)
2. Type `/agent` and select the "API Coverage Looper Agent"
3. Type `/fleet complete coverage` to launch a fleet to complete all the test writing in parallel


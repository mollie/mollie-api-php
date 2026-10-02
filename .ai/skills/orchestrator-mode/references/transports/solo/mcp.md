# Transport: Solo (MCP)

Default guideline for orchestrators operating inside [Solo](https://github.com/sublayerapp/solo).

## MCP Tooling

| Operation | Tool |
|---|---|
| **Spawn Delegate** | `mcp__solo__spawn_agent(agent_tool_id=N, name="<slug>")` (Returns `agent_instructions`) |
| **Push Input** | `mcp__solo__send_input(process_id=PID, input="<brief>")` |
| **Identify Parent** | `mcp__solo__whoami()` (Required for worker-to-orchestrator push) |
| **Harvest Delegate** | `mcp__solo__close_process(process_id=PID)` (Removes process from Solo) |
| **List Agents** | `mcp__solo__list_agent_tools()` |
| **Complete tracking item** | `mcp__solo__todo_complete(todo_id=N, project_id=<id>)` |

**Note on Spawn:** `spawn_agent` returns `agent_instructions`; you **MUST** prepend this string to the delegate's first `send_input` so the child knows its own identity.

### Dispatching Claude TUI delegates — park the brief, push a pointer

Large multiline briefs sent via `send_input` to a **Claude** TUI agent corrupt its input buffer: newlines fragment into unsent "queued messages", the render garbles, and the agent idles or derails. Reliable pattern:

1. Put the brief in the assignment's existing todo, artifact, or assigned scratchpad. If none exists, create one assignment pad and retain its ID. Keep an immutable brief reference if later status updates replace the current summary; do not also create `status` and `done` pads for the same assignment.
2. `send_input` a **single short line** pointing at it:
   `Call whoami() first, then read scratchpad_id=<N> (mcp__solo__scratchpad_read) and execute that brief in one pass.`
3. If a Claude agent is stuck with a `Press up to edit queued messages` buffer, flush it before respawning: `mcp__solo__send_input(process_id=PID, bytes=[13])` (raw Enter).

**Codex** tolerates big multiline `send_input` briefs — this workaround is Claude-TUI-specific.

For outside-Solo dispatch or chatty CLI reads, see [./cli.md](./cli.md).

## Pattern C Reporting Contract (Solo)

Use one assigned current-state pad through milestones and terminal delivery, then push its exact ID to the orchestrator. This protocol is self-contained; an installed `solo-orchestration` skill may supply transport details but does not require an additional `spawn-status` or `done` pad. Apply [state surfaces](../../state-surfaces.md) for retention and reading scope.

### Preamble to paste into every Solo brief:

```text
## Reporting contract (CRITICAL — do this first)

Call whoami first and use only your own identity.
Orchestrator pid: <PID_FROM_whoami>. Project: <PROJECT_ID>.
Assigned current-state scratchpad: <PAD_ID_OR_CREATE_ONCE_AND_REPORT_ID>.
Reuse this ID for the entire assignment. Read its current revision before updates.
Keep a concise current summary with head/version, blocker, next action and evidence
links. Store the full report once; no extra brief/report/done pad for the same work.

On terminal event (DONE/BLOCKED/MERGED):
1. Persist the full report/evidence and update the SAME assigned pad's terminal state
   and exact references. Preserve sole-copy evidence; use a revision guard.
2. THEN push once: mcp__solo__send_input(process_id=<ORCH_PID>, project_id=<PROJECT_ID>,
   input="<SENTINEL>: <one-line summary>. Scratchpad ID: <PAD_ID>; report: <REF>").
3. Print the sentinel as the final stdout line. The orchestrator owns verification,
   todo disposition, scratchpad archive and worker closure; do not self-accept.

Durable terminal state precedes the push so a swallowed callback can be recovered. Prefer MCP; if unavailable, use the CLI to update the same record by ID rather than creating a replacement. Once verification and todo linkage are complete, the orchestrator archives the assignment pad and closes the worker in one harvest action, even if a parent release stays open. Confirm direct-ID reads still work after archival.
```

## Todo completion at merge (Solo)

Solo tracking items are todos. The merge-is-atomic rule in [../../../workflows/review-and-merge.md](../../../workflows/review-and-merge.md) maps to Solo as:

- The coding delegate's PR body cites each originating todo with a machine-parseable line: `Resolves solo todo #N` (one per todo).
- The merger, in the SAME terminal action as `gh pr merge --squash`, calls `mcp__solo__todo_complete(todo_id=N, project_id=<id>)` for every `#N` the PR resolves. Merge and complete are one step — never one without the other.
- The orchestrator's post-merge hygiene re-reads those lines and verifies each `todo_complete` landed (belt-and-suspenders) — see [../../../workflows/hygiene.md](../../../workflows/hygiene.md).
- **Automation backstop:** a GitHub merge webhook / CI job can parse `Resolves solo todo #N` from the merged PR and call `todo_complete` with no agent in the loop — the durable systemic fix. Until that exists, the merger's atomic completion + orchestrator verification are the guarantee.

## Surface Routing

Prefer the MCP tool; the `solo …` CLI form is the fallback when MCP is unavailable.

| Surface | Tool (preferred) | CLI fallback | Purpose |
|---|---|---|---|
| **Solo scratchpad** | `mcp__solo__scratchpad_write` / `scratchpad_read` | `solo scratchpads` | Working artefacts for next-step agents. |
| **Solo todo** | `mcp__solo__todo_create` / `todo_update` / `todo_complete` | `solo todos` | Actionable work with criteria. Complete atomically at merge (see above). |
| **MemPalace** | `mempalace` MCP tools | — | Durable cross-session knowledge. |
| **Repo `docs/`** | `Write` / `replace` | — | Shipping artefacts. |

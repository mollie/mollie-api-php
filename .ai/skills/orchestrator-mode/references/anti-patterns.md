# Anti-patterns

Accumulated lessons. Re-read at session start.

| Anti-pattern | Fix |
|---|---|
| Dispatched code-modifying work via built-in in-process subagents. | In-process subagents (Claude Code `Task` / `Agent`) share orchestrator context and can't be easily isolated. Use them for **read-only research** only. Code-modifying delegates MUST be dispatched out-of-process via your transport. |
| Harvested an agent by pausing/stopping it without closing/removing the process. | After terminal sentinel + artifact verification, remove the stored process entry via your transport. Stop/pause is only a temporary fallback. |
| Reviewer dispatched on a docs-only PR. | Docs-only merges directly; reviewer table guides. |
| Single-voice plan review marked "good enough." | Multi-reviewer brief (≥2 voices). Codex catches what Claude misses and vice-versa. |
| Workspaces pile up (e.g. 10+ stale worktrees). | Cleanup orchestrator-side after every merge + at session orient. |
| Sentinel forgotten in brief → orchestrator over-sleeps. | Reporting contract preamble is non-negotiable in every brief. |
| Hardcoded agent tool IDs. | Resolve at dispatch via your transport's discovery. |
| Re-using one delegate PTY across multiple deliverables. | One agent per task. Spawn fresh. |
| Branch state assumed stale → wrong merge order claim. | `git fetch && git log origin/main..HEAD` before merge-order claims. |
| Asked the user "should I act autonomously per the north star?" after consult.md returned a valid load. | The loaded artefact IS the autonomy contract. After a successful load, do not re-ask about scope or direction — proceed. |
| Re-derived a north star inside a delegate brief because consult.md returned missing. | Delegates obey, do not author. On missing north star, the orchestrator (not the delegate) prompts the user once. |
| North star drifted; orchestrator silently picked one. | consult.md's drift check must surface the diff and ask. |
| Cross-project worker leaks state to the orchestrator's project. | The brief MUST name the target project explicitly for tracking items and durable state. |
| Used blocking-UI prompts (e.g. `AskUserQuestion`) while delegates in flight. | Sentinels arriving during the modal window get swallowed. Reconcile state surfaces after the modal returns; prefer plain-text questions during active dispatch. |
| Reached for the `solo` CLI while the Solo MCP server was connected. | Prefer MCP tools whenever reachable — they preserve parent/child routing and Pattern C push callbacks. The CLI is a fallback for when MCP is unavailable (e.g. outside Solo) or for chatty reads that compress better as shell output. |
| Merged a PR but left the originating tracking item open ("merged-but-open" clutter). | Merge is atomic with completion: the merger completes every originating tracking item in the SAME action as the merge, parsing the PR's `Resolves <tracking item> #N` lines. Orchestrator verifies post-merge (hygiene). Optional CI backstop auto-completes from the merged PR. |
| PR body omitted the `Resolves <tracking item> #N` line. | The merger can't know which item to close without it → merged-but-open. The coding-delegate brief mandates one `Resolves` line per closed item; it also drives the CI auto-completion backstop. |

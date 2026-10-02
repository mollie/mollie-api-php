# State surfaces

State can live in durable surfaces (scratchpads, todos, durable memory) or the repo. Each has one job; don't double-write.

| Surface | Purpose | Lifetime | Read by |
|---|---|---|---|
| **Durable Scratchpad** | Current state and evidence links for one active assignment, or an actively consumed plan. | Archive after verified harvest of that assignment. | Assigned worker and orchestrator. |
| **Tracking Item** | Actionable work with accept criteria (e.g. Solo todo). | Closed when done (with verification comment). | Orchestrator + fix agents. |
| **Evidence artifact** | Full report, logs, patch, immutable review verdict or prior revision. | Retain by stable reference after harvest. | Reviewers when that evidence is needed. |
| **Durable Memory** | Durable cross-session knowledge: design decisions, verbatim user directives, postmortems, lessons. | Permanent. Update in place when fact evolves. | Any future session via memory search. |
| **Repo `docs/`** | Shipping artefact versioned with code. | Versioned with codebase. | End users, future contributors. |
| **North star** (`docs/NORTH_STAR.md` + memory mirror) | Decision rule the orchestrator and every delegate read at dispatch time. Mission, non-goals, constraints, principles. Not a roadmap. | Persistent; refresh in place via `/north-star` refresh workflow. | Orchestrator on boot; every delegate via brief injection. |

## Naming conventions

Prefer `<kind>/<identifier>` for scratchpads:
- **kind**: review, plan, audit, brainstorm, handoff, research, done
- **identifier**: stable (e.g. repo-pr-N) or feature-slug

Reuse the known scratchpad ID for the same assignment. A retry, milestone, changed head or review round does not by itself need a new pad. Independent reviewers have separate assignments and must not overwrite each other's verdicts.

## Creation and reading budget

- Before creating a pad, check the tracking item's existing references. Give each active assignment at most one current-state pad; reuse it for progress and terminal status rather than creating separate `brief`, `status`, `report` and `done` pads.
- A separately consumed plan or immutable brief may need its own artifact. Prefer an existing todo or artifact link; if a scratchpad is the only durable surface, give the additional pad a named consumer and harvest condition.
- Keep the current summary roughly 300–500 words: owner, task, exact head/version, state, blocker, next action and evidence links. This is a routing target, not permission to omit required evidence. Preserve full reports in durable artifacts, or a clearly separated evidence section when no artifact store exists.
- Read the current revision before replacing stale status, and use a revision guard. Preserve unharvested findings and sole-copy evidence before compacting; never discard them merely to meet a length target. Append only new decision-relevant information, not repeated boilerplate or full command output.
- Use filtered metadata listings, known IDs and relevant sections for routing. Read required source and reports fully, but do not load every historical pad or print recursively embedded evidence into model context. Integrity checks can run mechanically; a hash match is not a substitute for a required semantic review.

## When to write what

- **Scratchpad:** current state another agent needs; survives PTY close. The todo owns acceptance criteria and dependencies, the report owns evidence; link rather than duplicate them.
- **Durable Memory:** durable rule, postmortem, design trade-off, verbatim user directive.
- **Tracking Item:** concrete follow-up with acceptance criterion.
- **Repo `docs/`:** ships with code (specs, ADRs, user guides).
- **Throwaway:** stdout only.

## Lifecycle

`active -> terminal awaiting harvest -> verified and linked -> archived`

The orchestrator owns the final transition. Verify the assignment's report, record its disposition and exact evidence references in the tracking item, then archive its status pad and close the worker in the same harvest action. A parent epic or release may remain open. A BLOCKED review can be harvested and archived once its findings and next owner are recorded; this does not resolve the blocker or complete the implementation task.

Before the next dispatch or handoff, reconcile pads against active assignments and unharvested reports. Archive verified historical pads; preserve live work, unresolved ownership and sole-copy evidence. Age, pad count, an idle timer or process exit alone is not proof of harvest. Archiving hides old records from discovery without deleting evidence; verify direct-reference retrieval remains possible in the chosen backend. Do not permanently delete to hit a count target.

Keep one current orchestrator summary and one current handoff per project. Historical snapshots remain linked evidence, not mandatory boilerplate for every successor. A handoff-only request stays read-only apart from writing its requested handoff; it does not authorize lifecycle cleanup of other records.

For Solo-specific mapping and tool calls, see [transports/solo/README.md](transports/solo/README.md).

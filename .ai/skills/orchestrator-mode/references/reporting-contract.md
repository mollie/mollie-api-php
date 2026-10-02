# Reporting contract

Every brief MUST start with a reporting preamble. It enables push reporting (worker calls back on terminal events; orchestrator wakes on push, not on idle false-positives).

## Role Discipline

Two primary signal strategies:
- **Push (Pattern C)**: Worker pushes a signal to the orchestrator. Avoids false-positives from idle transitions (e.g. while reading a long brief).
- **Pull (Timers)**: Orchestrator watches for idle state. Useful as a safety net for **external** state (CI, deploy, network polling).

Pick the strategy that fits your workflow.

Worker-to-orchestrator signal:
1. Worker identifies the orchestrator (e.g. via PID).
2. Worker applies Pattern C via its local tooling.
3. On terminal events (DONE/BLOCKED/MERGED), the worker pushes a signal.

Use the assignment's existing current-state pad for milestones and terminal status, with full evidence stored once and linked. Do not make a new `done` or review pad solely to send a callback. The orchestrator records acceptance or blocker disposition, archives the verified assignment pad, then closes the worker; a wider parent task may remain open. See [state surfaces](state-surfaces.md).

Sentinel vocabulary (use exact tokens — terminal stdout line + push signal):

| Worker kind        | Sentinel                                  |
|--------------------|-------------------------------------------|
| brainstorm         | BRAINSTORM DONE: <durable-ref>            |
| writing-plans      | PLAN DONE: <durable-ref>                  |
| plan reviewer      | REVIEW DONE: <CLEAN|NITS|BLOCKERS>        |
| counselors panel   | COUNSELORS DONE: <verdict>                |
| PR reviewer/merger | MERGED <sha>  OR  BLOCKED — <N> items filed |
| impl agent         | IMPL DONE: <PR url or merge sha>          |
| cleanup / hygiene  | CLEANUP DONE: <durable-ref>               |
| release engineer   | RELEASE READY: <release-url>              |
| any worker blocked | BLOCKED: <reason>                         |

`<durable-ref>` is a short reference handle pointing into the chosen transport's durable state (e.g. scratchpad slug for Solo, ticket ID for Linear, file path for filesystem); it is NOT the full payload.

## Sub-agent cascade

Coordinator-spawned sub-agents must use the same Pattern C contract, reporting terminal events to their coordinator via transport-specific push signals. The coordinator collapses panel-level events into a single sentinel for the main orchestrator.

## Signal Ambiguity

Idle-transition timers can fire when a worker is reading a brief or waiting for input. Push (Pattern C) wakes the orchestrator only when the worker declares a terminal event, which may reduce false-positive wakeups.

If no signal arrives, assume work is still running. Status checks are manual diagnostics.

## Reconciliation after blocking UI

Some host UIs (e.g. Claude Code's `AskUserQuestion`) block the orchestrator's main channel while waiting for user input. Push signals arriving during that window can be lost. After any blocking-UI prompt returns, reconcile in-flight delegate state by checking:

1. The assigned current-state pads and their terminal revisions since the last check, regardless of name (including legacy `done/*`).
2. Delegate processes whose status flipped to Stopped/Closed since last check.
3. Tracking items flipped to completed since last check.

Don't trust sentinel-absence during the modal window. Prefer plain-text questions over structured-UI prompts when delegates are mid-task. Reserve blocking UI for boot-time or between-wave decisions.

For Solo-specific tool calls and preamble text, see [transports/solo/README.md](transports/solo/README.md).

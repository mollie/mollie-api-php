# Pre-dispatch spec formalization

For non-trivial features. Skip the whole flow only when work is mechanical, a follow-up fix for a specific reviewer blocker, or docs-only.

```
1. BRAINSTORM (≥2 voices)
2. WRITING PLAN  (Claude /superpowers:writing-plans on brainstorm output)
3. COUNSELORS    (one coordinator agent runs the panel; orchestrator gets ONE doc)
4. SYNTHESIZE    (orchestrator reads consensus + unique insights, revises plan)
5. DISPATCH IMPL
```

When in doubt, run the cycle. 30-min spec cost ≪ 1-day wrong-impl cost.

## 1. Brainstorm — must be multi-voice

Single-agent brainstorm output is acceptable ONLY as preamble feeding into the dialogue. Final brainstorm scratchpad must show ≥2 voices. Acceptable patterns:

- **a) Parallel + synthesize.** Spawn 2 agents (different families, e.g. Claude + Codex) with same prompt. Each writes its own scratchpad. A third synthesizer agent reads both, has them iterate (≤3 rounds), produces final scratchpad.
- **b) Adversarial pair.** Spawn agent A with `/superpowers:brainstorming`. Spawn adversarial agent B (different family) to challenge A's sketch and produce the synthesized brainstorm.

## 2. Writing-plans

Spawn a Claude solo delegate that runs `/superpowers:writing-plans` on the brainstorm output. Produces ordered phases, file list, acceptance criteria, rollout gate.

## 3. Counselors coordinator

Spawn ONE Claude coordinator. The coordinator owns the panel + synthesis so the orchestrator gets ONE consolidated document. Coordinator brief instructs:

1. Spawn panel in parallel: Claude (deep reasoning), Codex (impl pragmatics), Gemini (dissent).
2. Brief each panelist with the [Multi-reviewer brief template](#multi-reviewer-brief-template). Assign each independent reviewer its own durable record (e.g. `review/plan-<topic>-<agent>`). Reuse it across that assignment's rounds; preserve prior verdicts as immutable evidence links, not extra visible status pads.
3. Harvest verdicts (Pattern C — coordinator is panel's orchestrator, not the main one). Sub-agents push terminal events to their coordinator via transport-specific push signals.
4. Synthesize into a consolidated scratchpad (e.g. `counselors/plan-<topic>`) with sections: Per-panelist verdict / Consensus blockers (≥2 voices) / Unique insights (one voice) / Recommended next step (DISPATCH IMPL / PATCH PLAN / REJECT-AND-REWRITE) / Consensus matrix.
5. Push a signal to the main orchestrator with summary + scratchpad slug. Sentinel: `COUNSELORS DONE: <verdict>`.
6. After each panelist's evidence and disposition are verified and linked, archive that panelist's pad and close the worker. Keep the consolidated plan visible while it has an active consumer. Do not erase dissent or substitute synthesis for required independent review.

"Counselors" = user-vocab for the panel.

Design intent: main orchestrator gets ONE doc not N; coordinator can iterate the panel mid-stream; Pattern C wakes orchestrator once not N times.

## 4. Synthesize

Read consensus blockers + unique insights. Revise plan (directly or via another Claude delegate). If changes are substantial, loop back to step 3 for r2 (typically Claude + Codex only on r2; full panel only on r1 unless major plan rewrite).

## When to skip

- Mechanical work (rename across files, agreed-upon fix).
- Follow-up fixes for a specific reviewer blocker (the blocker IS the spec).
- Docs-only work that doesn't change behaviour.

## Multi-reviewer brief template

```text
You are one of several independent reviewers of an implementation plan.
Do NOT coordinate with other reviewers. Do NOT produce a "balanced" view.
Your job is to be adversarial and find what the plan misses.

## Plan
<paste plan OR reference solo scratchpad slug>

## Your job
1. Read end-to-end. Check against project spec / reality.
2. Find: missing requirements, wrong assumptions, over-engineering,
   unstated dependencies, test gaps, rollback gaps, ops concerns.
3. Produce a structured verdict:
   - BLOCKERS (plan ships broken)
   - MISSING (omits something the spec requires)
   - OVER-SCOPED (does work we shouldn't)
   - ALTERNATIVES (different approaches worth considering)
   - UNIQUE INSIGHT (your distinctive angle)

## Output
Write the full verdict once and update your assigned durable record with its exact reference and terminal summary. Do not overwrite another reviewer's record or create a new pad for each round. Print `DONE` and the assigned record ID. Nothing else.

## Rules
- No consensus-seeking. No hedging. State your position plainly.
- File paths + line numbers for every concrete finding.
- Even if the plan is basically right, find one thing worth stress-testing.
```

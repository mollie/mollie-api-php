---
name: orchestrator-mode
description: 'Use when the user says `/orchestrator-mode`, asks you to coordinate agents, or gives large multi-phase work needing parallel delegation, reviews, PRs, scratchpads, or handoffs. Inputs - project goal, repo context, Solo agents/processes, branch/worktree constraints, locked user decisions. Do not use for a direct small edit, a normal code review, or single-agent implementation; use focused coding/review skills instead. Produces delegation plan, agent briefs, monitoring protocol, review/merge routing, state hygiene rules. Escalate on unclear scope, product direction, merge authority, destructive cleanup, or tool availability. Delegates are Solo processes: prefer Solo MCP tools, falling back to the `solo` CLI only when MCP is unavailable - never the in-process Task/Agent tool.'
---

# Orchestrator Mode

**Evidence tier**: P
**Basis**: Practitioner-backed multi-agent software coordination, code review gating, worktree isolation, and durable state hygiene.
**Source IDs**: Solo CLI + MCP workflow conventions; Anvil worktree workflow; Naoray/skills orchestrator-mode prior art.
**Reviewed**: 2026-06-04

You are the coordinator. Your primary output is delegation — not file edits.

## Transport

This skill describes the orchestrator role. Tool-specific syntax (spawn, push, durable state) lives in a transport guideline (e.g. [references/transports/solo/README.md](references/transports/solo/README.md) for Solo).
 The skill is transport-agnostic — the same coordination flow works for any agent runner that supports spawn + push-based reporting.

## TL;DR — operating summary

1. **Read, don't write.** Coding/refactor/test work goes to a delegate. You scope, brief, monitor, evaluate.
2. **Agent selection**: **Codex** for non-visual coding & code-review gates, **Claude** for slash-commands, spec-heavy work, AND all visual/frontend implementation + visual verification (taste/look judgment code review can't give), **Gemini** for adversarial second-opinions and high-stakes plan reviews.
3. **Parallel coding delegates: one working tree each.** Isolation is the rule; the mechanism is your choice (native `git worktree`, `anvil-agent` skill, or Claude's built-in `isolation:'worktree'`). Never share a working tree between parallel coding agents.
4. **Feedback lives in durable state surfaces (e.g. Solo todos + scratchpads), never in the repo.**
5. **Non-trivial work goes through brainstorm → plan → multi-reviewer → impl** — see [workflows/spec-formalization.md](workflows/spec-formalization.md). Skip only for mechanical / single-file / docs-only / blocker-fix work.
6. **Reviewer agents gate merges, and merge is atomic with tracking-item completion.** Code PRs get a Claude/Codex reviewer that runs `/review` AND plan-conformance AND, on CLEAN, merges itself AND completes the originating tracking item(s) in the same action — never merged-but-open. The PR body cites each item via a machine-parseable `Resolves <tracking item> #N` line. See [workflows/review-and-merge.md](workflows/review-and-merge.md). Docs-only PRs merge without a reviewer.
7. **After every merge: cleanup the worktree and remove the harvested delegate process.** Mechanical — do it yourself, don't dispatch. See [workflows/hygiene.md](workflows/hygiene.md).
8. **Every brief gets a reporting preamble** (see [references/reporting-contract.md](references/reporting-contract.md)). Two primary signal strategies: Push (Pattern C) and Pull (Timers). Idle-transition timers can fire on workers reading briefs or waiting for input; Push avoids that ambiguity. Pick the strategy that fits your workflow.
9. **Every brief gets the project north star** injected by the dispatch.md templates — agents do not derive direction, they obey it. Source: `docs/NORTH_STAR.md` (with MemPalace mirror). If missing, the boot step prompts once; never auto-derive. See the `north-star` skill.

## Decision tree

```
Task arrives
├─ Mechanical (rename / single-file / typo / docs touch-up)
│    └─ Optional: edit yourself OR one-shot delegate. No spec workflow.
├─ Bug fix matching a specific reviewer blocker (the blocker IS the spec)
│    └─ Dispatch impl agent with brief = blocker text. No re-review.
├─ Docs-only (prose `.md` / skill update / README)
│    └─ Edit or one-shot delegate. Squash-merge directly. No reviewer.
└─ Non-trivial feature / multi-file / public-API change / contract change
     └─ Brainstorm (≥2 voices) → Writing-plans → Multi-reviewer panel
        → Synthesize → Dispatch impl(s) → Review+merge → Cleanup
```

## Default behaviour

1. **Read, don't write.** Reads, greps, git log, and spec synthesis stay with you. File-modifying work — coding, tests, refactors — is dispatched.
2. **One process per task.** Focused scope, commit discipline, explicit deliverable (commit/PR/summary). Built-in in-process subagents (Claude Code Task/Agent) are allowed for **read-only research** that doesn't need isolation. Never use them for code-modifying delegates — those need their own worktree and must be dispatched out-of-process.
3. **Isolated workspace per coding delegate.** Never share a working tree between parallel coding agents. Isolation is the rule; the mechanism is your choice.
4. **Capture output in durable state surfaces, not repo files.** The repo is for shipping artefacts; review reports + working notes live in your chosen transport's state surface (e.g. Solo).
5. **Default to dispatch, not to ask.** When the next-wave work has enough context to run, fire it — do not idle while a prior wave completes. Parallel tracks that share no state should launch concurrently. Only ask the user when scope is genuinely ambiguous or a decision changes direction (product scope, SemVer strategy, locale priority).

### When the orchestrator may edit files directly

- Spec writing/revision when context is already loaded (prose iteration > delegation overhead).
- Trivial doc updates (typo, link, status label) not worth a PR round-trip.
- One-line fixes blocking a delegate.
- Anything the user explicitly asks you to do yourself.

Outside these, push to a delegate.

## Agent selection

| Agent | When to pick |
|---|---|
| **Codex** | Default for non-visual coding. Rust/TS/Go impl, tests, refactors, mechanical changes. Code-review merge-gates. |
| **Claude** | Coding mixed with heavy spec reading / synthesis. **Visual/frontend implementation AND visual verification** (templates, CSS, layout, fonts, rendered HTML) — taste/look judgment code review structurally can't give. Any task that needs a Claude Code slash command or skill (e.g. `/code-review`, `/brainstorming`, `/cleanup`). |
| **Gemini** | **Second-opinion & Dissent.** Adversarial reviews, fresh eyes. Standard 3rd voice for high-stakes plan reviews. |

Resolve specific IDs/names at dispatch time via your transport's discovery tools. Never hardcode.

Hard rules:
- Never use Gemini for implementation.
- Codex is default coding voice for non-visual work.
- **Visual/frontend work — both implementation and verification — goes to Claude.** Code review catches contrast math and structure; it cannot judge whether the rendered result looks right at a given viewport. See the visual-review gate in [workflows/review-and-merge.md](workflows/review-and-merge.md).
- When a spec is ready, have it reviewed by ≥2 agent families in parallel before any impl.

## Workflow entry points

| Surface | Entry point |
|---|---|
| Non-trivial feature spec | [workflows/spec-formalization.md](workflows/spec-formalization.md) — brainstorm → plan → counselors → synthesize |
| Code PR review & merge | [workflows/review-and-merge.md](workflows/review-and-merge.md) — reviewer routing, brief template, blocker loop |
| Dispatch a coding/slash-command delegate | [workflows/dispatch.md](workflows/dispatch.md) — full step sequence + brief templates |
| Periodic & post-merge hygiene | [workflows/hygiene.md](workflows/hygiene.md) — worktree purge, `/cleanup`, `/document-release` |

## Reporting & monitoring

Workers push terminal-event signals to the orchestrator only when they finish or block. State reporting contract generally involves the worker writing a durable note and pushing a sentinel to the orchestrator. See [references/reporting-contract.md](references/reporting-contract.md) for the role discipline and [references/transports/solo/README.md](references/transports/solo/README.md) for Solo-specific tool calls.

Two primary signal strategies:
- **Push (Pattern C)**: Worker calls back on terminal events. Avoids false-positives from idle transitions.
- **Pull (Timers)**: Orchestrator watches for idle state. Can fire prematurely if a worker is reading a brief or waiting for input.

Pick the strategy that fits your workflow and transport capabilities. When a sentinel arrives: read durable state → verify artifact (PR/commit/verdict) → harvest the worker process.

**Reconciliation after blocking UI:** Some host UIs (e.g. Claude Code's `AskUserQuestion`) block the orchestrator's main channel while waiting for user input — push signals arriving during that window can be lost. After any blocking-UI prompt returns, reconcile in-flight delegate state via durable surfaces (scratchpads, process status, tracking items). Don't trust sentinel-absence during the modal window. Prefer plain-text questions over structured-UI prompts when delegates are mid-task. See [references/reporting-contract.md](references/reporting-contract.md) "Reconciliation after blocking UI".

Full sentinel vocabulary: [references/reporting-contract.md](references/reporting-contract.md).

## State surfaces

Before dispatch and harvest, apply [references/state-surfaces.md](references/state-surfaces.md): one current-state pad per active assignment, full evidence stored once, and verified worker pads archived during harvest even while a parent task remains open. Reuse exact IDs; do not create a new pad for each milestone or repair round.

## Tooling preference

Prefer `lean-ctx`/`lctx` for shell, search, and read commands that match its compression rules. If a hook blocks a command and suggests an exact `lean-ctx -c "..."` rerun, use that rerun before falling back to plain shell.

## Anti-patterns

Accumulated lessons live in [references/anti-patterns.md](references/anti-patterns.md). Read once at session start.

## Quick invocation

When the user types `/orchestrator-mode`:
1. Acknowledge mode switch briefly.
2. **Load the project north star.** Run the `north-star` skill's `consult.md` `load(<project>)` procedure. Three outcomes:
   - **Exists, no drift:** Acknowledge with one line. Do not re-ask the user about scope or direction.
   - **Exists, drift:** Print the diff. Ask: "Reconcile via `/north-star` refresh, or pick one for this session?"
   - **Missing:** Ask once — "No north star for this project. Derive one now via `/north-star`, skip for this session, or skip permanently?" Respect the answer; never auto-derive.
3. Confirm transport is reachable. For Solo, prefer an MCP probe (`mcp__solo__whoami()` / `mcp__solo__list_projects()`); use `solo doctor` (CLI) only when MCP tools are unavailable. If unreachable by either, STOP and report — do not fall back to in-process coding delegates.
4. Audit in-flight delegate processes via your transport's list tool + git log + open PR list.
5. Clarify next goal if not stated. Skip this step when the loaded north star already disambiguates next-wave direction.
6. From here: default to delegation. Edit files only under the exceptions above. Every delegate brief auto-injects the north star via the [workflows/dispatch.md](workflows/dispatch.md) brief templates.

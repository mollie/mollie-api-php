# Dispatch workflow

**Transport: Reference your transport guideline (e.g. [../references/transports/solo/README.md](../references/transports/solo/README.md)) for specific tool syntax.**

This skill coordinates out-of-process delegates. Built-in in-process subagents (Claude Code `Task` / `Agent`) are allowed for **read-only research** that doesn't need isolation. Never use them for code-modifying delegates — those need their own worktree and must be dispatched out-of-process via your chosen transport.

For a coding task (file-modifying delegate).

```
0. TRANSPORT Identify the transport (e.g. Solo) and verify connectivity.
1. RESOLVE   Resolve the target agent tool id via your transport's discovery.
             NEVER hardcode IDs — they're env-specific.
2. SCOPE     Understand task. Read spec + code. Identify file surface. Parallelisable?
3. TRACK     Reuse the tracking item with scope and criteria; create only if missing.
             Assign one current-state pad ID per worker assignment, reusing existing
             references. Apply ../references/state-surfaces.md before creation.
4. SPAWN     Spawn the delegate process via your transport.
5. BRIEF     Deliver the full brief (see template below). If the transport
             returns bootstrap instructions (e.g. Solo's agent_instructions),
             prepend them to the first input.
             NOTE: for a Claude TUI delegate, do NOT paste a large multiline
             brief via send_input — it corrupts the input buffer. Park the
             brief in a scratchpad and push a one-line pointer instead. See
             your transport guideline (Solo: references/transports/solo/mcp.md
             "Dispatching Claude TUI delegates"). Codex tolerates big briefs.
6. MONITOR   Choose a signal strategy: Push (Pattern C) via your transport
             (e.g. send_input) or Pull (Timers). Push avoids false-positives
             from idle transitions. Sentinel arrives via transport push or
             timer firing.
             If you used blocking UI (e.g. `AskUserQuestion`) between dispatch
             and harvest, reconcile state surfaces (scratchpads / process
             status / tracking items) before assuming work is still pending —
             push signals can be lost during the modal window.
7. REVIEW    Verify commits, run tests, review diff + PR description.
8. HARVEST   After the artifact is verified, record disposition and evidence links
             in the tracking item, archive the assignment's status pad, then
             close/remove the delegate. Do not wait for the parent epic to close.
             If worktree orphaned,
             dispatch cleanup.
```

For a slash-command delegate (read-only or stateless action), use the slash-command brief template below and skip the worktree step.

## Subagent lifecycle hygiene

- One agent per focused task. Don't multi-stage one PTY through unrelated phases — stale context biases reasoning.
- After harvesting a delegate, delete or archive the workspace isolation (e.g. worktree) if the agent didn't.
- Never re-use the same PTY for two different deliverables.
- Record and archive verified worker state before removing the process. A sentinel or idle event alone does not establish acceptance.

## Workspace isolation

- Isolation is the rule. Never share a working tree between parallel coding agents.
- The agent owns its workspace setup. Orchestrator does not pre-create worktrees for the delegate — Step 0 in the brief tells the delegate to set up its isolation.

## North star injection

Every brief below has a `## Project north star` section near the top. Populate it via the `north-star` skill's `inject(<brief>)` procedure before dispatch.

## Brief template — coding delegate

```text
<paste Reporting contract preamble from your transport guideline>

---

## Project north star
<paste output of north-star inject() — six sections verbatim, or the "Not set" stanza>

When implementation choices fork, pick the path that serves the mission for
the target users without violating non-goals or constraints. Decision
principles break ties.

---

You are the <TOPIC> implementation worker for <PROJECT>. Work in an
isolated workspace (e.g. anvil worktree) branched off `<PARENT_BRANCH>`,
finish with a PR.

## Step 0 — Workspace
1. Set up an isolated workspace on branch `agent-<topic-slug>` from `<PARENT_BRANCH>`.
2. ALL edits happen there.
3. git status + git branch --show-current to confirm.

## Scope
<Pull from tracking item. Include file paths, acceptance criteria, spec section.>

## Deliverables (one commit per phase, [agent] prefix)

### Phase 1 — <name>
<scope>
Commit: `[agent] <type>(<scope>): <one-line subject>`

### Phase 2 — ...

## Step N — Push + open PR
git push -u origin HEAD
gh pr create --base <PARENT_BRANCH> --title "<title>" \
  --body "<summary + test plan>

Resolves <tracking item> #N"

The PR body MUST include a machine-parseable line citing every originating
tracking item this PR closes — one `Resolves <tracking item> #N` line per
item (substitute your transport's concrete form per your transport guideline).
The merger parses these to complete the items atomically with the merge, and
the same line drives the optional CI auto-completion backstop. No `Resolves`
line = the merger can't know what to close = a "merged-but-open" tracking item.
Always include it.

## Rules
- Isolated workspace only. Never edit main repo working tree.
- [agent] prefix on every commit.
- Run tests before every commit; iterate until green.
- Stage specific files by name; never `git add -A`.
- No amending. No force-push. No skipping hooks.

## Output format
- File follow-ups via your transport's tracking tool (e.g. Solo todos).
- Update the assigned current-state pad; do not create separate progress/report/done pads. Store the full report once and link it. Preserve required evidence and unharvested findings.
- At end: print PR URL + sentinel + push signal to orchestrator. Nothing else.

Start now with Step 0.
```

## Brief template — slash-command delegate

```text
<paste Reporting contract preamble from your transport guideline>

---

## Project north star
<paste output of north-star inject() — six sections verbatim, or the "Not set" stanza>

---

You are running <SLASH_COMMAND> on <TARGET>. Read-only dispatch — do not
create worktrees or branches.

## Task
Invoke `<SLASH_COMMAND>` with <ARGS>. Examples: `/review 123`, `/qa`, `/brainstorming <topic>`.

## Output
- Concise stdout summary.
- Structured findings → the assigned durable record; update its terminal state and link full evidence rather than creating another report pad.
- Follow-ups → your transport's tracking tool (e.g. Solo todos).

When done: print payload/slug (if any) + one-line verdict + sentinel.
```

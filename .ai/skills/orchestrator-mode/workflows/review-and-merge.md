# Post-dispatch review + merge

**Scope: code PRs only.** Pure docs / skill / prose updates with no behavioural impact merge directly after a quick sanity read — don't burn a reviewer agent on markdown.

For code PRs, dispatch a fresh reviewer per PR with a combined brief covering code-quality AND plan-conformance. The reviewer gates the merge.

```
1. DISPATCH REVIEWER  Pick per routing table. Reviewer does:
                      a) /review (or codex review) for quality
                      b) Plan-conformance (diff PR scope vs originating todo/spec)
                      Verdict:
                        CLEAN    → merges (rebase for feature-target, squash for main)
                                   + completes originating tracking item(s) IN THE SAME ACTION
                                   + deletes remote branch, closes workspace
                        NITS     → merges + completes originating tracking item(s)
                                   + files follow-up tracking items for the nits
                        BLOCKERS → does NOT merge; does NOT complete;
                                   files tracking items + PR review comment
2. EVALUATE FEEDBACK  Orchestrator reads reviewer summary. For BLOCKERS:
                      dispatch fix agent on new isolated workspace → cycle to step 1.

3. POST-MERGE         git checkout main; git pull --ff-only; git fetch --prune;
                      list remaining todos; decide next wave;
                      cleanup the merged branch's worktree.
```

## Merge is atomic with tracking-item completion (HARD RULE)

The agent that squash-/rebase-merges a PR MUST, **in the same terminal action**, complete the originating tracking item(s) — the merge and the completion are one indivisible step, not two. Never merge without completing; never leave a "merged-but-open" tracking item.

- On **CLEAN / NITS**: merge AND complete every originating tracking item the PR resolves (one `complete` per item). NITS additionally file *new* follow-up items for the nits — those are separate from the originating item, which is still completed.
- On **BLOCKERS**: do not merge and do not complete — the work isn't done.
- The PR body names which items it resolves via a machine-parseable line (see [dispatch.md](dispatch.md) brief template — `Resolves <tracking item> #N`). The merger reads those lines to know exactly which items to complete.
- Transport specifics — the concrete completion tool and the exact `Resolves` form — live in your transport guideline (Solo: [../references/transports/solo/mcp.md](../references/transports/solo/mcp.md)).

> **Automation backstop (recommended, durable fix).** The agent-in-the-loop completion above is the immediate guarantee; the systemic fix is a merge-hook / CI action that parses `Resolves <tracking item> #N` from the merged PR and auto-completes the item with no agent involved. With the backstop live, the in-action completion becomes belt-and-suspenders. Until then, the merger's atomic completion + the orchestrator's post-merge verification (see [hygiene.md](hygiene.md)) are the guarantee.

## Visual-review gate (UI-changing PRs)

**Trigger:** the PR changes UI / visual output — templates, CSS, layout, fonts, rendered HTML, anything a user sees rendered. Code review (Codex/Claude code-gate) verifies contrast math, structure, and convention but **structurally cannot catch a rendered-output regression** (e.g. a mobile `<=390w` hero-title clip that passes every contrast check). Add this gate **after the code-review gate returns CLEAN and before merge.**

```
1. CODE GATE     Reviewer returns CLEAN (quality + plan-conformance) — as above.
2. VISUAL GATE   Dispatch a Claude visual-QA delegate. It:
                 a) renders the app / preview locally,
                 b) captures screenshots via Playwright MCP
                    (fall back to a cached/bundled browser if system Chrome absent),
                 c) compares against brand / design intent at BOTH desktop
                    AND mobile widths (e.g. 1280w + 390w),
                 d) returns PASS or ISSUES.
                 ISSUES: each issue + screenshot ref, filed as tracking todos.
3. ROUTE         Merge ONLY when code-clean AND visual PASS.
                 ISSUES → dispatch a Claude fix delegate on the SAME branch,
                          then re-run the visual gate. Cycle until PASS.
```

Sentinel (add to the visual-QA delegate's reporting contract):

| Role | Terminal sentinel |
|---|---|
| visual reviewer | `REVIEW DONE: <PASS\|ISSUES>` |

Non-visual code PRs skip this gate. Docs-only PRs skip both gates.

### Visual-QA delegate brief template

```text
<paste Reporting contract preamble from your transport guideline>

You are the VISUAL-QA reviewer for PR <URL> (branch <BRANCH>).
Do NOT merge — you only return a visual verdict.

## Your job
1. Fetch the branch + set up an isolated workspace.
2. Render the app / preview locally (start dev server or build static preview).
3. Capture screenshots with the Playwright MCP at BOTH desktop (e.g. 1280w)
   AND mobile (e.g. 390w) widths. If system Chrome is absent, fall back to a
   cached/bundled browser.
4. Compare against brand / design intent (reference the live brand site or
   design spec). Look for clipping, overflow, misalignment, font/scale breaks,
   contrast-in-context, responsive regressions.
5. Decide: PASS or ISSUES.

## On PASS
- Write the verdict to the assigned visual-review record; reuse its ID for follow-up rounds and retain prior verdicts as evidence.
- Print `REVIEW DONE: PASS`.

## On ISSUES
- For each issue: file a tracking todo with description + screenshot ref + viewport.
- Store the full verdict with screenshot references once, then link it from the assigned current-state pad. If no separate artifact store exists, keep the full verdict in that same pad.
- Print `REVIEW DONE: ISSUES` + count.

## Rules
- Do not merge, push, or comment on the PR. Verdict only.
- Screenshots/refs live in durable state, never the repo.
```

## Reviewer routing

| Task | Default reviewer | Rationale |
|---|---|---|
| Code PR merge-gate (small-medium scope) | **Codex** | Catches Cargo cycles, type-system gotchas, regex edges, fail-closed regressions. |
| Spec-heavy code PR (multi-doc reading, skill invocation, architectural synthesis) | **Claude** | Spec-reading depth + `/review`/`/qa`/`/audit` skill stack. |
| Docs-only PR (prose `*.md`) | **None — orchestrator merges** | Don't burn a code-review voice on prose. |
| High-stakes code PR (release-blocking, security-adjacent, multi-module refactor) | **Dual: Claude + Codex parallel** | Orthogonal blocker classes. Orchestrator synthesizes before merge. |
| Plan formalization multi-review | **Dual: Claude + Codex** + Gemini adversarial 3rd | Codex catches impl pragmatics; Claude catches contract drift; Gemini finds dissenting edge cases. |

You don't need dual reviews on everything — utilize Codex by default, dual for high-stakes.

## Reviewer brief template

```text
You are reviewing and potentially merging PR <URL>.

## Context
- Origin: Tracking item (e.g. solo todo) #<ID> — <title>.
- Spec section(s): <file:line refs>
- Counselors / prior review artefacts: <scratchpad slug / file path>

## Your job
1. Run `/review <PR>` (or codex review) for quality, security, convention.
2. Plan-conformance: re-read tracking item + spec, diff PR vs scope, flag missing/extra.
3. Sanity tests: fetch branch, run project test command, confirm green.
4. Decide: CLEAN / NITS / BLOCKERS.

## On CLEAN or NITS
- Merge (rebase-merge for feature-branch targets, squash for main).
- ATOMIC: in the same action, complete every originating tracking item the PR
  resolves (parse the `Resolves <tracking item> #N` lines from the PR body —
  one completion per item). Never merge without completing.
- For NITS: open follow-up tracking item per nit with scope + rationale. (These
  are NEW items; the originating item is still completed above.)
- Delete remote branch.
- Print merge SHA + completed item IDs + "MERGED".

## On BLOCKERS
- Do NOT merge.
- Post one PR review comment summarising every blocker (file:line + required fix).
- File a tracking item per blocker.
- Print "BLOCKED — <count> items filed".

## Rules
- Use scribe / gh / transport CLI as needed.
- Persist the structured verdict before mutating PR state, and update the assigned review record with its exact reference. Reuse that record for the assignment rather than creating separate status/report/done pads. The orchestrator archives it after verified harvest, including BLOCKED reviews whose findings remain tracked.
- Do not push or comment anything else.
```

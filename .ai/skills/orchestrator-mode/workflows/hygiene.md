# Periodic hygiene

## Worktree cleanup — mechanical, orchestrator-side

Stale anvil worktrees accumulate fast (deps + Herd links + certs + DBs per branch). 10+ stale worktrees can consume GB and drag system perf. Mechanical work — do it yourself, don't dispatch.

Rule:
1. **After every PR merge:** remove that branch's worktree (`anvil remove <branch> --force`).
2. **Before every wave-boundary status:** list worktrees + remove any whose branch is merged or whose worker is closed.
3. **After a release cut:** force-remove ALL except `main` and the orchestrator's worktree.
4. **At session start (orient phase):** list worktrees. If >5 stale, purge before dispatching new work.

```bash
anvil list                       # or `git worktree list`
for branch in <merged-list>; do
  anvil remove "$branch" --force # or `git worktree remove <path> --force`
done
df -h /                          # log delta if you care
```

Save freed-space to MemPalace as calibration data (AAAK e.g. `worktrees.purged.11+disk.freed.45gb`).

## Scratchpad + todo hygiene — mechanical, orchestrator-side

State surfaces accumulate across waves (`done/*` scratchpads, finished tracking todos, stale-and-vague todos) and start lying about what's live. Prune so the surfaces reflect ONLY live work. Mechanical — orchestrator does it directly, does not dispatch.

Run **after harvesting a delegate (once its artifact is verified)** and **at every wave boundary**:

1. **Finish the assignment's lifecycle.** Verify the report, record disposition and exact evidence references in its tracking item, archive its current-state pad, then close the worker. Apply this to any pad name, including `spawn-*-status`, not only `done/*`. A parent release remaining open is not a reason to retain a harvested worker pad. A harvested BLOCKED verdict remains a blocker in the todo; archival is not task completion. Preserve active or unharvested records and verify archived references remain readable. See [state surfaces](../references/state-surfaces.md).
2. **Verify originating tracking item is completed (belt-and-suspenders).** The merger is required to complete the originating tracking item(s) atomically with the merge (see [review-and-merge.md](review-and-merge.md) "Merge is atomic"). After every merge, the orchestrator CONFIRMS those items are actually marked complete — read the PR's `Resolves <tracking item> #N` lines and check each item's status. If the merger missed one, complete it now. A merged PR with a still-open originating item is a hygiene defect: fix it on sight.
3. **Close only satisfied tracking.** Complete a review assignment when its own acceptance criteria are verified, even if its verdict is BLOCKED. Keep the unresolved implementation, parent and blocker todos open. Filing a verdict, archiving its pad or closing its reviewer does not satisfy those tasks; complete them only when their own acceptance criteria pass.
4. **Re-evaluate open todos.** Walk the open list:
   - drop obsolete ones (superseded, abandoned, duplicated),
   - re-prioritize the rest against the current north star / next wave,
   - split anything stale-and-vague into concrete actionable items.

At each wave boundary, reconcile visible pads with active workers, unharvested reports and independently consumed plans. Report only unexplained records or failed archival; do not create a new cleanup scratchpad. A count increase from a new active worker can be legitimate. Never delete evidence or archive live work to force a fixed count.

## Slash-command hygiene delegates

Both run as Claude solo delegates (slash-command-driven, no worktree needed):

- **`/cleanup`** — scans for stale plans/specs/orphan files. Dispatch after any wave that closed 3+ todos.
- **`/document-release`** — updates README/ARCHITECTURE/CONTRIBUTING/CLAUDE.md + opens `docs/release-<version>` PR. Dispatch after any merge that changed public API/CLI surface/user-visible behaviour.

Bad times: after every commit (noise); mid-active feature (conflicts).

```text
# /cleanup brief
Run /cleanup on <repo>. Focus: completed plans under docs/plans/, stale
docs/roadmap/ for shipped versions, outdated README sections (vs VERSION),
orphaned fixtures/design files. Don't delete load-bearing items without
flagging. Summary → the existing cleanup tracking item or assigned status pad.
```

```text
# /document-release brief
Run /document-release on <repo>. Compare current main (or feature branch)
against last tagged release. Update README/ARCHITECTURE/CONTRIBUTING/CLAUDE.md
to match reality. Open PR on `docs/release-<version>` targeting main (or the
feature branch if pre-release). No source changes. No spec edits for
intentionally-deferred work.
```

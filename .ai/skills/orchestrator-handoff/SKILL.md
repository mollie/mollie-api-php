---
name: orchestrator-handoff
description: Preserve coordinated agent work in a read-only handoff and restart prompt when requested or context is tight. Skip ordinary status updates; clarify missing state or requested mutations.
---

# Orchestrator Handoff

**Evidence tier**: P
**Basis**: Practitioner-backed session handoff, incident-transfer, and distributed coordination workflows.
**Source IDs**: Solo MCP scratchpad/process workflow; MemPalace durable-memory workflow; Naoray/skills orchestrator-handoff prior art.
**Reviewed**: 2026-05-12

Generate a handoff prompt when:
- User signals session-switch intent ("time for a handoff", "start a new orchestrator").
- Context window budget getting tight.
- In-flight work spans hours and benefits from a fresh PTY.

## What the handoff captures

Pull from three sources + synthesize into one prompt:

1. **Solo state** — `list_processes`, `todo_list completed=false`, `scratchpad_list`, open timers.
2. **Git/PR state** — `gh pr list`, `gh issue list`, `git log` on active branches.
3. **MemPalace durable memory** — `mempalace_search wing=<project>` for locked decisions, north star, rulebooks, drawers recorded this session.

## Output format

Update the project's existing current handoff by ID, or create `handoff/<project-slug>` if none exists. Read its revision first and preserve any sole-copy evidence before replacement. Keep one current routing summary; historical snapshots can remain linked artifacts rather than additional visible handoffs.

### Required sections

- **Session snapshot** — date, orchestrator pid (will change in new session), what was shipped today.
- **In-flight agents** — list of running solo process IDs + what they're doing + how to harvest them.
- **Active timers** — pending idle-timers on which processes.
- **Open PRs** — per-PR: URL, state, who needs to act, link to review scratchpad.
- **Open decisions** — user-facing questions queued, with options + orchestrator's lean.
- **Locked decisions (last 24h)** — with MemPalace drawer IDs for depth.
- **Active scratchpads** — which ones new orchestrator should preserve vs. can archive.
- **Next intended wave** — what the orchestrator would dispatch next if it continued.

### Optional sections (include when relevant)

- North star / project compass (cite drawer ID — don't duplicate content).
- Calibration data reference (task runtimes, reviewer reliability).
- Adopter context (who the users are, what they're waiting on).

## Process

1. Query `mcp__solo__whoami` for orchestrator pid.
2. Query live process and tracking metadata, open PRs/issues and relevant memory in parallel. Use known scratchpad IDs and filtered lists; read current load-bearing records, not the entire historical collection.
3. Synthesize. Do NOT dump raw tool output — summarize.
4. Update the current handoff with a revision guard, or create it once if missing. Include owners, exact heads, unresolved gates, evidence references and next actions. Link full reports instead of copying them or recursively requiring all predecessor handoffs. Preserve explicitly required full reads and unique unresolved historical evidence.
5. Print the consumable prompt — a condensed instruction the user pastes into a new Claude Code session that starts with `/orchestrator-mode` + `/solo-orchestration` invocation + references the handoff scratchpad.

## Rules

- Handoff is a READ-ONLY synthesis. Do not stop in-flight agents. Do not archive scratchpads. Do not close processes.
- Don't echo MemPalace drawer contents into the scratchpad — just reference the drawer IDs. Keeps scratchpad small + durable.
- Keep one current handoff ID per project. Record the timestamp in its snapshot; a later session does not need a new pad merely because its date or orchestrator changed.

## Example trigger invocation

User says: "time for a handoff" → orchestrator invokes this skill → produces scratchpad + prints 400-word prompt → user copies prompt into a fresh orchestrator session.

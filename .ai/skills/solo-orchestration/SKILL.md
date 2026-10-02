---
name: solo-orchestration
description: Coordinate Solo MCP agents with push-based reporting when spawning or orchestrating Solo work. Excludes other agent frameworks, single-session work, and one-off terminal processes.
---

# Solo Orchestration — Push, Don't Poll

Solo MCP spawned agents can report back to their orchestrator without the orchestrator polling on a timer. This skill defines the three push patterns and when to use each.

## Why This Matters

Polling burns tokens and cache:
- Every `get_process_output` check is a tool round-trip on the orchestrator
- Recurring `timer_set(..., loop=true)` injects user turns even when nothing changed
- 5-minute poll interval × hour-long task = 12 wasted context refreshes

Push-based reporting: orchestrator idle until spawn signals. Zero cost when nothing happens.

## The Three Patterns

### Pattern A — Idle-triggered timer (safety net)

Orchestrator registers one-shot timer that fires when spawn enters idle state.

```
timer_fire_when_idle_any(
  processes=[SPAWN_PID],
  max_wait_ms=1800000,
  body="Spawn SPAWN_PID went idle. Read assigned STATUS_RECORD_TYPE/STATUS_RECORD_ID. If unavailable, inspect process output; do not invent a replacement pad. Idle is not completion. Decide from evidence whether to harvest, continue waiting, or report a blocker."
)
```

Use when: spawned agent cannot call back (non-agent runtime, external process, untrusted prompt).

Downside: idle != done. Spawn may be idle mid-task waiting for input.

### Pattern B — `send_input` callback (true push)

Spawn calls `send_input(orchestrator_pid, "...")`. Delivered as fresh user turn in orchestrator PTY.

Orchestrator must share its pid **and project_id** with spawn at launch. Use `whoami` to get both, then inject into spawn prompt. See "Cross-Project Scoping" below — this is the #1 footgun.

Use when: spawn is a Solo agent runtime (Claude/Codex/Gemini) you control.

Downside: spawn might forget to call back. Always pair with Pattern A as safety net.

### Pattern C — Combo (default, recommended)

1. Spawn reuses the assignment's supplied status record at milestones; create one `spawn-{SPAWN_PID}-status` pad only when no durable status record was assigned. Update current state rather than appending a growing transcript.
2. Spawn `send_input` orchestrator on terminal events (DONE/BLOCKED/FAILED)
3. Orchestrator registers idle-timer with generous `max_wait_ms` as safety net
4. Orchestrator reads the assigned record on callback, verifies and links the evidence in the todo, then archives the worker pad before closing the worker. The wider parent task may remain open.

Cheapest steady state. Resilient to spawn forgetting callback.

### Pattern D — Wake-the-spawn (stalled-worker rescue)

`timer_set` accepts `delivery_process_id=N` — the timer body is injected into THAT process's PTY as a fresh user turn, not the orchestrator's. Use this when a spawn is suspected stalled mid-task and should self-recover without orchestrator round-tripping.

```
# Schedule a self-rescue ping after the runtime's normal first-response budget.
# Local OpenCode/Qwen inference needs at least 5 minutes per model round.
# Body is delivered as a fresh user turn TO THE SPAWN (not orchestrator).
timer_set(
  delay_ms=300000,
  body="If your last task is still running, ignore this. If you stalled or are waiting for input, "
       "update your assigned STATUS_RECORD_TYPE/STATUS_RECORD_ID with current state and send_input "
       "the orchestrator (pid={ORCH_PID}, project_id={ORCH_PROJECT_ID}) with 'SPAWN {SPAWN_PID} STALLED'.",
  delivery_process_id=SPAWN_PID,
)
```

Use when: spawn task has known steady-state (e.g., bounded reasoning round) and silence past that point usually means agent forgot to call back, not that it's still working. Cheaper than orchestrator waking itself, reading output, then sending a nudge — one timer = one push into the spawn.

For local OpenCode/Qwen agents, the TUI can show `0 tokens` until the first streamed completion finishes. This is not a liveness signal. Allow at least 5 minutes for each model round, including the follow-up round after a tool call. Only classify the process as stalled when that budget has elapsed and output has stopped changing, or when the process exits or logs an explicit error/abort.

Anti-pattern: do NOT use Pattern D as primary wake. Pair with Pattern B (send_input callback) as the happy path. Pattern D is for the case where the spawn went quiet without signalling.

Caveat: timer body is delivered verbatim into the spawn's PTY. Keep wording plain — no XML, no backticks-heavy payloads, no secrets. The spawn agent will read it as if the user typed it.

## Cross-Project Scoping (critical)

Solo MCP tools resolve `process_id` and scratchpad names in the **caller's** project scope. Orchestrator and spawn often live in different projects (orchestrator spawns a worker into the codebase being worked on). Without explicit `project_id`, spawn's `send_input(ORCH_PID, ...)` resolves in spawn's project → pid not found → Solo falls back to closest Claude in spawn's project → wrong session receives the callback.

Rules:
- Orchestrator captures `project_id` from `whoami()` — not just `process_id`.
- Spawn prompt includes `{ORCH_PROJECT_ID}`.
- Spawn passes `project_id={ORCH_PROJECT_ID}` on every `send_input` to orchestrator.
- Pick one project to host the scratchpad. Recommended: orchestrator's project. Spawn passes `project_id={ORCH_PROJECT_ID}` on `scratchpad_write` / `scratchpad_append`. Orchestrator reads without override.
- Same-project orchestration: `project_id` is harmless to pass → always pass it. Zero-cost safety.

## Spawn Prompt Template

Prepend to first `send_input` after `spawn_agent(...)` (or `spawn_process(kind="agent")` — generic equivalent). Also prepend the `agent_instructions` returned by the spawn call.

```
You are spawn process {SPAWN_PID}. Orchestrator pid: {ORCH_PID}. Orchestrator project_id: {ORCH_PROJECT_ID}.

Task: {TASK_DESCRIPTION}

Reporting contract:
1. Reuse the status record assigned in the brief (an existing todo is sufficient).
   If none was assigned, create ONE "spawn-{SPAWN_PID}-status" scratchpad and keep
   its ID. For scratchpad updates, read its revision and name, then use
   scratchpad_write(scratchpad_id=ID, name=CURRENT_NAME, project_id={ORCH_PROJECT_ID},
   expected_revision=REV, content=...). For an assigned todo, use todo_get and the
   todo update/comment tools instead; preserve acceptance criteria and dependencies.
   Keep current
   owner, head, blocker, next action and exact evidence links. Preserve sole-copy
   or unharvested evidence before replacing stale status. Do not create separate
   brief/report/done pads or duplicate full reports in callbacks.
2. On terminal event (DONE, BLOCKED, FAILED) call:
   send_input(
     process_id={ORCH_PID},
     project_id={ORCH_PROJECT_ID},
     input="SPAWN {SPAWN_PID} {status}: {one-line summary}. Status record: {RECORD_ID}. Report: {REF}"
   )
3. Always pass project_id on send_input and scratchpad calls targeting orchestrator — your default scope differs.
4. Do NOT send_input for intermediate progress — scratchpad only.

Orchestrator has idle-timer safety net. send_input is preferred path.
Expected work duration: {DURATION_ESTIMATE}.
```

## Orchestrator Recipe

```
# 1. Resolve own pid + project_id FIRST
me = whoami()
orch_pid = me.process_id
orch_project_id = me.project_id

# 2. Spawn (spawn_agent is the preferred API; spawn_process(kind="agent", ...) still works)
result = spawn_agent(agent_tool_id=N, name="worker-1")
spawn_pid = result.process_id

# 3. Seed spawn with reporting contract (inject orch_pid AND orch_project_id)
send_input(spawn_pid, AGENT_INSTRUCTIONS + "\n\n" + SPAWN_PROMPT_FILLED)

# 4. Register safety-net timer
timer_fire_when_idle_any(
  processes=[spawn_pid],
  max_wait_ms=2 * expected_duration_ms,
  body=f"Spawn {spawn_pid} idle. If no callback received, read assigned {status_record_type}/{status_record_id}; inspect process output if missing, without creating another record. Decide next step from evidence."
)

# 5. Stop orchestrator activity. Wait for user-turn injection from either path.
```

## Efficiency Rules

- The assigned durable status record > send_input for progress. `send_input` injects user turn = full orchestrator re-render cost.
- Reserve `send_input` for terminal events only. One call per spawn lifecycle is ideal.
- At most one current-state pad per assignment; use an already assigned todo instead when sufficient. Keep the routing summary roughly 300–500 words and store full evidence once with stable links. The size target never authorizes evidence loss or skipping required review.
- `max_wait_ms` = 2× expected duration. Too short = premature fires. Too long = no safety.
- After verified harvest: record disposition and evidence references in the todo, archive the worker's pad if one exists, then `close_process(spawn_pid)`. This also applies to harvested BLOCKED reviews; archive does not complete the unresolved implementation. Verify archived references remain readable. Idle/exit alone is not harvest.
- Parallel spawns: one timer per spawn OR `timer_fire_when_idle_all([pids...])` if orchestrator waits for full fan-in.

## Anti-Patterns

Do NOT:
- `timer_set(60000, "check spawn", loop=true)` — burns cache on empty ticks
- `get_process_output` in a sleep loop — same as above
- Poll scratchpad with recurring timer — push model makes this pointless
- Short `max_wait_ms` (<30s) as retry mechanism — use Pattern B callback instead
- Forget the harvest sequence — verified linkage, scratchpad archive, then `close_process`; orphaned records and pids accumulate
- Seed spawn with `orch_pid` but omit `orch_project_id` — callback silently misroutes when projects differ
- Use Pattern D (`delivery_process_id`) as primary wake mechanism — it bypasses the orchestrator and turns the spawn into a self-poller. Reserve for stalled-spawn rescue, pair with Pattern B as happy path.

## Multi-Vendor Preflight

Spawning a non-Claude runtime (Codex, Gemini, Amp, OpenCode) only works if that runtime has Solo MCP wired into its own config. Solo does NOT auto-inject. Without the wiring, the spawned agent has no `whoami` / `scratchpad_write` tools and cannot follow any reporting contract.

Run this preflight **once at the start of any multi-agent workflow**. It is the single source of truth for "which vendors can actually participate right now". Downstream skills (e.g. `solo-multi-agent-brainstorm`) MUST consume its output instead of building their own probe.

### What the preflight does

```
1. enabled = list_agent_tools()                         # Solo's own truth
2. For each tool: check vendor's user-config file for an `solo` MCP entry
3. If entry missing: patch idempotently (backup file as .bak-<unix-ts> first)
4. Health probe in parallel — spawn each, send minimal "whoami + scratchpad_write probe-{vendor}-{ts} + close"
   - Remote/hosted runtimes: timeout 30s per vendor (Claude needs >20s on cold boot)
   - Local OpenCode/Qwen: allow at least 300s per model round; do not use the TUI's `0 tokens` display as a liveness signal
5. Classify each vendor:
     OK            → ready to use
     NEEDS_AUTH    → CLI booted but blocked on interactive auth screen (Gemini, Amp)
     CLI_MISSING   → CLI binary not on Solo's subshell PATH (Solo runs nvm-managed shell)
     BOOT_LOOP     → CLI exits before accepting input (e.g. self-update on first run)
     MCP_MISSING   → MCP entry patched but agent still cannot resolve solo tools
     SUBMIT_BUG    → CLI accepts text but never executes (Codex needed raw CR `[13]`)
6. Archive probe scratchpads. Return: [(tool_id, name, status, hint)]
```

The downstream skill picks vendors with `status == OK`. Anything else is surfaced to the user with the install/auth hint and excluded.

### Vendor config registry

Solo MCP binary path: `/Applications/Solo.app/Contents/MacOS/mcp` (on macOS). Use the absolute path in every vendor config — none of these CLIs resolve `solo` from PATH the same way Claude does.

| Vendor | Config file | Format | MCP entry shape |
|--------|-------------|--------|-----------------|
| Claude | `~/.claude.json` | JSON | `mcpServers.solo = {type:"stdio", command:"<solo-mcp>", args:[], env:{}}` (Solo writes this on first install — usually already present) |
| Codex | `~/.codex/config.toml` | TOML | `[mcp_servers.solo]\ncommand = "<solo-mcp>"\nargs = []` |
| Gemini | `~/.gemini/settings.json` | JSON | `mcpServers.solo = {command:"<solo-mcp>", args:[]}` |
| Amp | TBD (only created after interactive login — `amp` first-run blocks on `Would you like to log in?`) | TBD | TBD |
| OpenCode | TBD (only created after `/connect` provider selection in TUI) | TBD | TBD |

Marked TBD = not yet validated end-to-end. First time a probe classifies one as `MCP_MISSING`, capture the working format and add it here. Do NOT guess.

### Idempotency rules for the patch step

- Read existing config first. If `solo` MCP entry already present with matching command path → skip, do not rewrite.
- Backup before any write: copy to `<file>.bak-<unix-ts>` (millisecond precision unnecessary).
- Use vendor-native config tooling where it exists. Otherwise hand-edit with surgical `Edit`/`Write` — never overwrite the whole config blindly, you will lose unrelated user settings.
- After write, re-read and parse to confirm valid syntax (TOML / JSON). Roll back from backup on parse failure.

### What preflight must NOT do

- **No auto-install of CLIs.** If `gemini` is not on PATH, surface an install hint (`npm i -g @google/gemini-cli`) and mark `CLI_MISSING`. Installing software without consent is out of scope.
- **No automated submission of credentials beyond reading user-provided env vars.** Preflight may read `GEMINI_API_KEY` from the host shell to inject into a spawn. Preflight must NOT prompt for a credential, fetch one, persist one, or send any string that could be a credential to a third party.
- **No browser-based OAuth flows.** Cannot be driven from PTY. Mark `NEEDS_AUTH` and surface the manual command for the user to run.
- **No Trust-Folder dialog clicks.** Hint user to set `trust_level = "trusted"` in vendor config (Codex), or accept the dialog interactively.
- **No new vendor registration in Solo's `list_agent_tools` registry.** That is a Solo-app concern.

### Auth strategies (Tier A + Tier C)

Several runtimes (Gemini, Amp, OpenCode) sit on an interactive auth prompt on first run. There is no agent layer running yet, so the spawned process cannot call `send_input` back to the orchestrator — the agent literally does not exist until auth completes. Preflight handles this with two complementary tiers.

#### Tier A — Env-var auth (preferred)

When the user has set the vendor's API-key env var in their shell profile, Solo injects it into the spawn via the agent-tool's `env` block (configured once per vendor in Solo's tool registry, not per spawn). The CLI sees the credential, skips the auth prompt, boots straight into the agent layer.

Known env-var registry:

| Vendor | Env var | Effect |
|--------|---------|--------|
| Claude | `ANTHROPIC_API_KEY` (rare; usually CLI auths via Claude Max session) | Skips browser-OAuth on first run |
| Codex | `OPENAI_API_KEY` (optional; CLI also supports per-account login) | Skips OpenAI login dialog |
| Gemini | `GEMINI_API_KEY` (or `GOOGLE_API_KEY` for Vertex) | Skips "Sign in with Google / API Key / Vertex" picker |
| Amp | `AMP_API_KEY` (verify on first successful spawn) | Skips `Would you like to log in?` prompt |
| OpenCode | Provider-specific (`ANTHROPIC_API_KEY`, `OPENAI_API_KEY`, …) | May still require `/connect` once; verify per provider |

Add new entries to this table as preflight observations confirm them. Do not guess — credential env-var names vary per vendor and version.

The user's responsibility: set the env var. Preflight's responsibility: read and surface in the spawn. Neither party persists credentials in the skill repo.

#### Tier C — Detect-and-pause (fallback)

When env-var auth is not in play (user has not set it, or the prompt is not env-var-skippable), the spawned CLI sits at an interactive prompt. Preflight detects this and surfaces a precise hint instead of timing out generically.

Detect-pattern registry (sampled from real first-run output):

```
AUTH_PATTERNS = {
  "amp":      ["Would you like to log in to Amp"],
  "opencode": ["Run /connect to add an AI provider"],
  "gemini":   ["How would you like to authenticate", "Trust folder"],
  "codex":    ["Sign in with ChatGPT", "Authenticate with OpenAI"],
}
```

Preflight calls `get_process_output(pid, lines=80)` once after the boot wait. If output matches a known pattern → classify `NEEDS_AUTH` and surface:

> **NEEDS_AUTH:** `{vendor}` boot is blocked on `"<matched pattern>"`. Run `{vendor}` once manually in a terminal to complete the prompt, then re-run the multi-agent workflow. Or set `{env_var}` in your shell profile to skip the prompt entirely.

Tier C is fail-safe — even if a vendor's prompt text changes and no pattern matches, the probe simply times out and the vendor is classified as `BOOT_LOOP` or `MCP_MISSING` rather than silently corrupting the run. Pattern-based detection just makes the user-facing message specific.

#### What preflight does NOT do (Tier B explicitly excluded)

Scripted TUI navigation — sending `y\n` to confirm a login prompt, picking option 2 from a menu, typing an API token in response to a string match — is **out of scope**. Reasons:

- TUI strings change vendor-side without warning → brittle
- Sending tokens via `send_input` puts them in scratchpad-adjacent telemetry → security regression
- Browser-OAuth flows cannot be scripted at all

If a user wants this they can set the env var (Tier A) or complete auth manually (Tier C).

### Footguns

- **Solo subshell uses nvm-managed Node (not your default).** A CLI installed under Node 20 is invisible to a Solo spawn that boots Node 23. Install global CLIs under the same Node version Solo uses (check `nvm current` inside a Solo terminal spawn).
- **First-run self-updates kill the process.** Codex `npm install -g` on boot, exits with "Please restart Codex". Always run a warm-up spawn (close it, spawn again) before classifying. The 2nd spawn is the real probe.
- **`send_input` text not always submitted.** Some TUIs swallow `\n`. Probe success means "the prompt actually executed", not "bytes were sent". Verify via `get_process_output` looking for tool-call evidence. Codex specifically may need a raw CR (`bytes=[13]`) follow-up.
- **MCP entry can be syntactically present but unloadable** (wrong path, wrong format, wrong field name per vendor). The probe's `whoami` call is the only ground-truth health check.
- **Probe scratchpads accumulate.** Always archive on probe exit. Tag `probe-multivendor`.

### Slow local inference

For OpenCode backed by local oMLX/Qwen, allow at least 300000 ms per model round. Reset the allowance after every completed tool result before the next model continuation. OpenCode records `step-start` with zero usage and updates tokens only at `step-finish`, so a 0-token display is not a liveness signal.

Do not cancel, rescue, or classify a stall before the full allowance unless the process exits or an explicit provider/server error appears. Pattern D timers for these runtimes must be at least 300000 ms; normal safety timers remain 2x expected duration. Define success or failure from terminal callbacks, tool evidence, process exit, or provider errors, not token-counter movement.

### Skipping the preflight

You may skip when:
- Workflow is single-vendor (Claude only) AND Solo's bundled Claude wiring is known-good
- You already ran preflight in the same orchestrator session and no vendor configs changed since

Otherwise: run it. The cost (~30s warm, ~60s cold) is paid back on the first prevented mid-flight stall.

## When To Skip This Skill

- Single orchestrator session, no spawns
- Terminal-only spawns (`kind="terminal"`) — no agent runtime to bind timers to
- External processes registered via `register_agent` — cannot receive timer wake-ups
- Synchronous subtask via Agent tool (different mechanism, handled by host)

## Quick Reference

| Need | Use |
|------|-----|
| Spawn will call back | Pattern B (send_input) |
| Spawn can't call back | Pattern A (idle-timer) |
| Mixed / untrusted / default | Pattern C (combo) |
| Spawn went quiet — push nudge into spawn | Pattern D (`timer_set(delivery_process_id=SPAWN_PID, ...)`) |
| Wait for all parallel spawns | `timer_fire_when_idle_all` |
| Wait for first done | `timer_fire_when_idle_any` |
| Progress visibility mid-task | Update the assigned record with concise current state |
| Terminal event signal | send_input to orch_pid |
| Multi-vendor workflow startup | Multi-Vendor Preflight (this skill) |
| Vendor config formats / Solo MCP entry shapes | Vendor config registry table above |

## Changelog

### 2026-05-27 — Solo API surface update

- **spawn API**: code samples use `spawn_agent(agent_tool_id=...)`. `spawn_process(kind="agent", ...)` documented as equivalent older API.
- **Pattern D added**: `timer_set(delivery_process_id=SPAWN_PID, ...)` injects timer body into the SPAWN's PTY instead of the orchestrator's. Use for stalled-spawn rescue. Anti-pattern: do not use as primary wake — Pattern B+A still happy path.
- **Quick Reference + Anti-Patterns** updated for both.

# Transport: Solo (CLI) — fallback

The MCP tools in [./mcp.md](./mcp.md) are the default. Use the CLI patterns below only as a fallback: when the Solo MCP server is not connected (e.g. orchestrating from outside Solo), or for chatty reads that compress better as shell output. When MCP is reachable, prefer the MCP equivalent — it preserves parent/child routing and Pattern C callbacks.

## CLI Tooling

Invoke via `ctx_shell` or `Bash`.

```bash
solo processes list --project-id <id> [--json]
solo processes spawn --project-id <id> --kind agent --agent-tool-id <id> --name <slug> --arg "<brief>"
solo processes get <pid> [--json]
solo processes stop <pid>
solo todos create --project-id <id> --title "<t>" --body "<b>"
solo scratchpads read <id> --project-id <id> --mode content
solo scratchpads create --project-id <id> --name <slug> --content <text>
```

Add `--json` only when you need structured parsing — human-output compresses better with lean-ctx.

The `scratchpads create` example is for a missing assignment record only. Otherwise update the known ID using the installed CLI's supported update command. Apply the same [record lifecycle](../../state-surfaces.md) as MCP; falling back to CLI must not create a duplicate status or terminal pad.

For the default MCP-based workflow, see [./mcp.md](./mcp.md).

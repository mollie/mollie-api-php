# Solo Transport

Guidance for coordinating agents over the [Solo MCP Server](https://github.com/sublayerapp/solo).

**Prefer MCP tools over the CLI.** MCP is the default for every operation that has an MCP equivalent — it preserves parent/child routing and Pattern C push callbacks. Reach for the CLI only as a fallback: when the Solo MCP tools are not connected (e.g. orchestrating from outside Solo), or for chatty reads that compress better as shell output.

- [mcp.md](./mcp.md) (Default) — MCP tools and Pattern C preamble. Use these whenever the Solo MCP server is reachable.
- [cli.md](./cli.md) (Fallback) — `solo` CLI patterns for when MCP is unavailable, plus chatty reads that benefit from compression.

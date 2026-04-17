# Webhook payload fixtures

These JSON files are verbatim copies of the example webhook payloads from
the Mollie staging documentation:

- Source: https://staging.docs.mollie.com/reference/webhooks
- Captured: 2026-04-17

They document the real shape of a webhook POST body as delivered by Mollie
to subscribers. In particular, the `_embedded` map keys the embedded entity
by its **resource type** (e.g. `payment-link`), not by the literal key
`entity`. This differs from the `GET /v2/events/{id}` response which uses
`_embedded.entity`.

## Files

- `payment-link-paid.full.json` — full payload with embedded resource
  snapshot under `_embedded["payment-link"]`.
- `payment-link-paid.simple.json` — simple payload without any embedded
  resource data (entityId-only subscription).

Fixtures here are used by `tests/Webhooks/RealPayloadShapeTest.php` to
guard against regressions that would break real-world webhook delivery.

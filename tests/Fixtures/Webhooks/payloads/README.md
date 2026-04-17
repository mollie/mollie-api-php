# Webhook payload fixtures

These JSON files mirror example webhook payloads from the Mollie
documentation so tests can exercise realistic shapes without hitting a
live sandbox.

- Reference: https://staging.docs.mollie.com/reference/webhooks
- Captured: 2026-04-17

Mollie keys the embedded resource under `_embedded.entity`. The SDK's
`WebhookEventMapper` still iterates `_embedded` key-agnostically, so any
future schema tweak (extra sub-blocks, renamed key) will not silently
break webhook handling.

## Files

- `payment-link-paid.full.json` — full payload with the embedded
  resource snapshot under `_embedded.entity`.
- `payment-link-paid.simple.json` — simple payload without any embedded
  resource data (entityId-only subscription).

Fixtures here are used by `tests/Webhooks/RealPayloadShapeTest.php`.

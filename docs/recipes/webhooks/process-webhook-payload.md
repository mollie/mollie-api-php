---
title: Process webhook payloads
description: Map Mollie webhook payloads to concrete event handlers and resources.
---

- Map incoming POST body to an event handler:

  $mapper = new \Mollie\Api\Webhooks\WebhookEventMapper();
  $event = $mapper->processWebhookPayload($payload, $optionalConnector);
  if (! $event) {
      // Unknown/unsupported event type
  }

- Access event metadata and embedded resource:

  $type = $event->event->getType();
  $resource = $event->event->getResource();

Connector behavior

- Without connector: returns a lightweight `StandaloneResource` for simple data access.
- With connector: hydrates a strongly-typed resource; follow-up API calls are available.

Extensibility

- Register custom event handlers using `WebhookEventMapper::register($type, $handlerClass)`.
- Register or override resource mappings via `ResourceRegistry`.

Validation and errors

- `WebhookEventMapper` validates required fields: `id`, `type`, `entityId`, `createdAt`.
- Throws `InvalidArgumentException` on invalid payloads.

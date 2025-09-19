# Process webhook payloads

Webhooks are your application's way of staying in the loop with Mollie's payment events. When something important happens (like a payment being completed, refunded, or failed), Mollie sends a notification to your endpoint. This guide shows you how to safely process these webhook payloads and extract the juicy details you need.

```php
// 🔎 Signature verification...
// ...

// 🎉 if verified Webhook, we can process it
$event = (new WebhookEventMapper())->processPayload($request->getParsedBody());

// Extract the entity ID (e.g., payment ID, customer ID, etc.)
$entityId = $event->entityData('id');

// 🚀 Get the full resource object for direct interaction
// This only works if you subscribe to full event payloads
$resource = $event->entity()->asResource($mollie);
```

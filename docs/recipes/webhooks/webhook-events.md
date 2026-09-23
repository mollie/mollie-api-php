# Webhook Events

How to retrieve and manage webhook events using the Mollie API.

## Get a Webhook Event

```php
use Mollie\Api\Http\Requests\GetWebhookEventRequest;

try {
    // Get a specific webhook event
    $webhookEvent = $mollie->send(
        new GetWebhookEventRequest(
            id: 'whev_abc123'
        )
    );

    echo "Webhook Event {$webhookEvent->id}:\n";
    echo "- Resource: {$webhookEvent->resource}\n";
    echo "- Event Type: {$webhookEvent->type}\n";
    echo "- Entity ID: {$webhookEvent->entityId}\n";
    echo "- Created: {$webhookEvent->createdAt}\n";

    // getEntity() returns ?stdClass — null when the event carries no snapshot
    $entity = $webhookEvent->getEntity();

    if ($entity !== null) {
        echo "- Entity Data Available: Yes\n";
        // The embedded entity is a raw stdClass, so get_class() would always
        // report "stdClass". Read its own `resource` field instead.
        echo "- Entity Type: {$entity->resource}\n";
    } else {
        echo "- Entity Data Available: No\n";
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## Using Endpoint Collections (Legacy Style)

```php
try {
    // Get a webhook event using endpoint collections
    $webhookEvent = $mollie->webhookEvents->get('whev_abc123');

    echo "Event Type: {$webhookEvent->type}\n";
    echo "Entity ID: {$webhookEvent->entityId}\n";

    // Access the embedded entity data
    if ($webhookEvent->hasEntity()) {
        $entity = $webhookEvent->getEntity();
        echo "Entity data is available\n";
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## The Response

```php
$webhookEvent->resource;     // "event"
$webhookEvent->id;           // "whev_abc123"
$webhookEvent->type;         // "payment-link.paid"
$webhookEvent->entityId;     // "pl_4Y0eZitmBnQ5jsBYZIBw" (the payment link ID)
$webhookEvent->createdAt;    // "2023-12-25T10:30:54+00:00"
$webhookEvent->_embedded;    // stdClass holding the embedded entity, or null
$webhookEvent->_links;       // Object containing relevant URLs
```

## Working with Embedded Entity Data

The webhook event contains the full payload of the triggered event in the `_embedded` property:

```php
// Get the embedded entity — null when the event carries no snapshot
$entity = $webhookEvent->getEntity();

if ($entity !== null) {
    // For payment-link.paid events this is the raw payment link payload. It is
    // a plain stdClass: no typed resource, no isPaid() helper, and no `status`
    // field — payment links do not have one. `amount` is absent on an
    // open-amount link, so read it defensively.
    $amount = $entity->amount ?? null;

    echo "Payment Link ID: {$entity->id}\n";
    echo 'Amount: '.($amount === null ? 'open amount' : "{$amount->value} {$amount->currency}")."\n";
    echo 'Paid: '.(! empty($entity->paidAt) ? 'yes' : 'no')."\n";
}

// Direct access to _embedded structure
$embedded = $webhookEvent->_embedded;
if (! empty($embedded->entity)) {
    $entity = $embedded->entity;
    echo "Entity ID: {$entity->id}\n";
}
```

## Helper Methods

The `WebhookEvent` resource includes convenient methods for working with entity data:

```php
// Check if entity data is available
if ($webhookEvent->hasEntity()) {
    echo "Entity data is embedded in this event\n";
}

// Get the entity data — getEntity() returns ?stdClass, so guard for null
$entity = $webhookEvent->getEntity();
if ($entity !== null) {
    echo "Entity ID: {$entity->id}\n";
    echo "Entity Resource: {$entity->resource}\n";
}
```

## Event Types and Their Payloads

Currently, only payment link events are supported:

### Payment Link Events
```php
// payment-link.paid
if ($webhookEvent->type === 'payment-link.paid') {
    $paymentLink = $webhookEvent->getEntity();

    if ($paymentLink === null) {
        throw new \RuntimeException('Payment-link event has no embedded entity.');
    }

    $amount = $paymentLink->amount ?? null;

    echo "Payment Link: {$paymentLink->id}\n";
    echo 'Amount: '.($amount === null ? 'open amount' : "{$amount->value} {$amount->currency}")."\n";
    echo 'Paid: '.(! empty($paymentLink->paidAt) ? 'yes' : 'no')."\n";
}
```

The embedded entity is the raw JSON payload as `stdClass`, not a `PaymentLink`
resource: there is no `isPaid()` helper, `amount` is a plain object rather than
a `Money`, and there is no `status` field. For a fully typed `PaymentLink` —
with `isPaid()` and a typed `Money` amount — process the delivery through
`WebhookEventMapper` and call `asResource()`; see [Webhooks](../../webhooks.md).

## Additional Notes

- **Event Structure**:
  - All webhook events have the `resource` property set to "event"
  - The `type` property indicates what kind of event occurred
  - The `entityId` references the ID of the object that triggered the event
  - The `_embedded.entity` contains the full object data at the time of the event

- **Entity Data**:
  - The embedded entity contains the complete state of the object when the event occurred
  - This is useful for getting the full context without making additional API calls
  - Entity data may be null for some event types
  - Payment links have no `status` field; derive the state from `paidAt`, `expiresAt` and `archived`
  - `amount` is absent on an open-amount payment link

- **Event Identification**:
  - Use the `id` property to uniquely identify webhook events
  - The `entityId` property tells you which object triggered the event
  - The `type` property tells you what kind of event occurred

- **Event Ordering**:
  - Webhook events are not guaranteed to be delivered in order
  - Use the `createdAt` timestamp to determine the actual order of events
  - Handle events idempotently in case duplicates are received

- **Processing Events**:
  - Always check if entity data is available using `hasEntity()`
  - `getEntity()` returns `null` when no entity is embedded, so guard the result
  - The entity data reflects the state at the time the event was created

- **Security**:
  - Always verify webhook signatures when processing events
  - The webhook event data shows what was sent, but verify authenticity
  - Consider webhook events as notifications, not the primary source of truth

- **Debugging**:
  - Use the webhook event ID to track specific events
  - The embedded entity data helps understand what triggered the event
  - Check the `_links` property for related URLs and actions

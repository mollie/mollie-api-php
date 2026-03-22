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

    // Check if event has embedded entity data
    if ($webhookEvent->hasEntity()) {
        $entity = $webhookEvent->getEntity();
        echo "- Entity Data Available: Yes\n";
        echo "- Entity Type: " . get_class($entity) . "\n";
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
$webhookEvent->entityId;     // "tr_abc123" (the payment link ID)
$webhookEvent->createdAt;    // "2023-12-25T10:30:54+00:00"
$webhookEvent->_embedded;    // Object containing embedded entity data
$webhookEvent->_links;       // Object containing relevant URLs
```

## Working with Embedded Entity Data

The webhook event contains the full payload of the triggered event in the `_embedded` property:

```php
// Check if entity data is available
if ($webhookEvent->hasEntity()) {
    // Get the embedded entity
    $entity = $webhookEvent->getEntity();

    // For payment-link.paid events, this would be the payment link data
    echo "Payment Link ID: {$entity->id}\n";
    echo "Payment Link Status: {$entity->status}\n";
    echo "Amount: {$entity->amount->value} {$entity->amount->currency}\n";
}

// Direct access to _embedded structure
$embedded = $webhookEvent->_embedded;
if (!empty($embedded->entity)) {
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

// Get the entity data
$entity = $webhookEvent->getEntity();
if ($entity) {
    echo "Entity ID: {$entity->id}\n";
    echo "Entity Status: {$entity->status}\n";
}
```

## Event Types and Their Payloads

Currently, only payment link events are supported:

### Payment Link Events
```php
// payment-link.paid
if ($webhookEvent->type === 'payment-link.paid') {
    $paymentLink = $webhookEvent->getEntity();
    echo "Payment Link: {$paymentLink->id}\n";
    echo "Amount: {$paymentLink->amount->value} {$paymentLink->amount->currency}\n";
    echo "Status: {$paymentLink->status}\n";
}
```

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
  - Use `getEntity()` to safely access embedded entity data
  - The entity data reflects the state at the time the event was created

- **Security**:
  - Always verify webhook signatures when processing events
  - The webhook event data shows what was sent, but verify authenticity
  - Consider webhook events as notifications, not the primary source of truth

- **Debugging**:
  - Use the webhook event ID to track specific events
  - The embedded entity data helps understand what triggered the event
  - Check the `_links` property for related URLs and actions

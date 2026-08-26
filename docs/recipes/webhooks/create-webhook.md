# Create a Webhook

How to create webhooks to receive notifications about events using the Mollie API.

## The Code

```php
use Mollie\Api\Http\Requests\CreateWebhookRequest;
use Mollie\Api\Types\WebhookStatus;
use Mollie\Api\Webhooks\WebhookEventType;

try {
    // Create a webhook using the direct request (new style)
    $webhook = $mollie->send(
        new CreateWebhookRequest(
            url: 'https://example.com/webhook',
            name: 'Payment notifications',
            eventTypes: WebhookEventType::PAYMENT_LINK_PAID
        )
    );

    $status = $webhook->status instanceof WebhookStatus
        ? $webhook->status->value
        : $webhook->status;

    echo "Webhook created: {$webhook->id}\n";
    echo "URL: {$webhook->url}\n";
    echo "Name: {$webhook->name}\n";
    echo "Event Types: " . implode(', ', $webhook->eventTypes) . "\n";
    echo "Status: {$status}\n";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## Using Endpoint Collections (Legacy Style)

```php
use Mollie\Api\Webhooks\WebhookEventType;

try {
    // Create a webhook using endpoint collections
    $webhook = $mollie->webhooks->create([
        'url' => 'https://example.com/webhook',
        'name' => 'Payment notifications',
        'eventTypes' => WebhookEventType::PAYMENT_LINK_PAID
    ]);

    echo "Webhook created: {$webhook->id}\n";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## The Response

```php
$webhook->resource;    // "webhook"
$webhook->id;          // "wh_4KgGJJSZpH"
$webhook->url;         // "https://example.com/webhook"
$webhook->profileId;   // Profile ID, or null when no profile applies
$webhook->createdAt;   // "2023-12-25T10:30:54+00:00"
$webhook->name;        // "Payment notifications"
$webhook->eventTypes;  // Array of subscribed event type strings
$webhook->status;      // WebhookStatus case, or an unknown raw string
$webhook->_links;      // Object containing webhook links
```

## Available Event Types

Use the `WebhookEventType` constants for the event types supported by your integration. Check the Mollie documentation for the latest availability.

```php
use Mollie\Api\Webhooks\WebhookEventType;

WebhookEventType::PAYMENT_LINK_PAID;  // "payment-link.paid"
WebhookEventType::SALES_INVOICE_PAID; // "sales-invoice.paid"
WebhookEventType::PROFILE_VERIFIED;   // "profile.verified"

// Get all available event types
$allEventTypes = WebhookEventType::getAll();
// Returns their raw string values
```

## Additional Notes

- **Webhook URL Requirements**:
  - There are no security requirements for test mode. However, in order to receive live mode events, the URL needs to be secured with an up-to-date HTTPS connection.
  - Must respond with HTTP 200 status code
  - Cannot be `localhost` URLs (use ngrok for local testing)

- **Event Types**:
  - Check out [the Mollie documentation](https://docs.mollie.com/reference/webhooks-new#event-types) to see what events are supported.
  - Additional event types will be added in future updates
  - Subscribe only to events you need to reduce noise

- **Webhook URL Best Practices**:
  - Use HTTPS for security
  - Implement proper authentication/authorization
  - Handle webhooks idempotently (same webhook may be sent multiple times)
  - Log webhook payloads for debugging
  - Return 200 OK as quickly as possible

- **Testing**:
  - Use ngrok or similar tools for local development
  - Test webhook handling with the webhook test endpoint
  - Verify signature validation works correctly

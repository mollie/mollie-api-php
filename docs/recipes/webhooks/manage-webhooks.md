# Manage Webhooks

How to retrieve, update, delete, test, and list webhooks using the Mollie API.

## Get a Webhook

```php
use Mollie\Api\Http\Requests\GetWebhookRequest;

try {
    // Get a specific webhook using direct request
    $webhook = $mollie->send(
        new GetWebhookRequest(
            id: 'wh_4KgGJJSZpH'
        )
    );

    echo "Webhook {$webhook->id}:\n";
    echo "- URL: {$webhook->url}\n";
    echo "- Name: {$webhook->name}\n";
    echo "- Event Types: {$webhook->eventTypes}\n";
    echo "- Status: {$webhook->status}\n";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## Update a Webhook

```php
use Mollie\Api\Http\Requests\UpdateWebhookRequest;
use Mollie\Api\Types\WebhookEventType;

try {
    // Update webhook properties
    $webhook = $mollie->send(
        new UpdateWebhookRequest(
            id: 'wh_4KgGJJSZpH',
            url: 'https://updated-example.com/webhook',
            name: 'Updated webhook name',
            eventTypes: WebhookEventType::PAYMENT_LINK_PAID
        )
    );

    echo "Webhook updated successfully\n";
    echo "New URL: {$webhook->url}\n";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## Delete a Webhook

```php
use Mollie\Api\Http\Requests\DeleteWebhookRequest;

try {
    // Delete a webhook
    $mollie->send(
        new DeleteWebhookRequest(
            id: 'wh_4KgGJJSZpH'
        )
    );

    echo "Webhook deleted successfully\n";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## Test a Webhook

```php
use Mollie\Api\Http\Requests\TestWebhookRequest;

try {
    // Test webhook delivery
    $result = $mollie->send(
        new TestWebhookRequest(
            id: 'wh_4KgGJJSZpH'
        )
    );

    echo "Webhook test initiated\n";
    echo "Test status: Success\n";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "Webhook test failed: " . htmlspecialchars($e->getMessage());
}
```

## List All Webhooks

```php
use Mollie\Api\Http\Requests\GetPaginatedWebhooksRequest;

try {
    // List all webhooks with pagination
    $webhooks = $mollie->send(
        new GetPaginatedWebhooksRequest()
    );

    foreach ($webhooks as $webhook) {
        echo "Webhook {$webhook->id}:\n";
        echo "- URL: {$webhook->url}\n";
        echo "- Name: {$webhook->name}\n";
        echo "- Event Types: {$webhook->eventTypes}\n";
        echo "- Status: {$webhook->status}\n";
        echo "- Created: {$webhook->createdAt}\n\n";
    }

    // Handle pagination if needed
    if ($webhooks->hasNext()) {
        $nextPage = $webhooks->next();
        // Process next page...
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## Using Endpoint Collections (Legacy Style)

```php
try {
    // Get a webhook
    $webhook = $mollie->webhooks->get('wh_4KgGJJSZpH');

    // Update a webhook
    $webhook = $mollie->webhooks->update('wh_4KgGJJSZpH', [
        'url' => 'https://updated-example.com/webhook',
        'name' => 'Updated name'
    ]);

    // Delete a webhook
    $mollie->webhooks->delete('wh_4KgGJJSZpH');

    // Test a webhook
    $mollie->webhooks->test('wh_4KgGJJSZpH');

    // List webhooks
    $webhooks = $mollie->webhooks->page();

    // Use convenience methods on webhook resource
    $webhook = $mollie->webhooks->get('wh_4KgGJJSZpH');

    // Update using convenience method
    $webhook->update([
        'name' => 'New name'
    ]);

    // Delete using convenience method
    $webhook->delete();

    // Test using convenience method
    $webhook->test();
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## The Response

```php
$webhook->resource;    // "webhook"
$webhook->id;          // "wh_4KgGJJSZpH"
$webhook->url;         // "https://example.com/webhook"
$webhook->profileId;   // "pfl_v9hTwCvYqw"
$webhook->createdAt;   // "2023-12-25T10:30:54+00:00"
$webhook->name;        // "Payment notifications"
$webhook->eventTypes;  // "payment-link.paid"
$webhook->status;      // "enabled"
$webhook->_links;      // Object containing webhook links
```

## Additional Notes

- **Webhook Updates**:
  - All parameters are optional when updating
  - Only specify fields you want to change

- **Webhook Testing**:
  - Tests webhook delivery by sending a test payload
  - Useful for verifying webhook URL accessibility
  - Check your webhook endpoint logs to confirm receipt
  - Test payloads may differ from actual event payloads

- **Webhook Deletion**:
  - Permanently removes the webhook
  - Cannot be undone
  - Stop receiving notifications for deleted webhooks immediately

- **Listing Webhooks**:
  - Returns all webhooks in your account
  - Results are paginated
  - Use `hasNext()` and `next()` for pagination

- **Convenience Methods**:
  - Webhook resources include convenience methods (`update()`, `delete()`, `test()`)
  - These methods operate on the current webhook instance
  - Simplify common operations without needing to specify the webhook ID again

- **Error Handling**:
  - Webhook operations may fail due to network issues
  - Handle `ApiException` to catch and respond to errors appropriately
  - Common errors include webhook not found (404) or validation errors (422)

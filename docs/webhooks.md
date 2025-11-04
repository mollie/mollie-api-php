# Webhooks

Mollie uses webhooks to notify your application about events that occur in your Mollie account. This guide explains how to work with Mollie webhooks in your application securely and efficiently. Mollie currently supports two types of webhooks: the (soon to be labeled) "legacy webhooks" and "next-gen webhooks". This guide is about the next-gen webhooks.

## Overview

Webhooks are HTTP POST requests that Mollie sends to your application when specific events occur, such as when a payment is completed, failed, or refunded. This allows your application to respond to these events in real-time without constantly polling the Mollie API.

## Security

### Signature Verification

**Important**: Always verify webhook signatures to ensure requests come from Mollie and haven't been tampered with.

Mollie signs all webhook requests with an HMAC-SHA256 signature sent in the `X-Mollie-Signature` header. The SDK provides built-in signature verification:

```php
use Mollie\Api\Webhooks\SignatureValidator;
use Mollie\Api\Exceptions\InvalidSignatureException;

$signingSecret = "your_webhook_signing_secret_from_dashboard";

try {
    $validator = new SignatureValidator($signingSecret);

    /**
     * Validate the webhook signature
     *
     * This method will throw an InvalidSignatureException if:
     * - A signature header is present but doesn't contain a valid signature
     * - The payload has been tampered with
     *
     * For PSR-7 requests, you can also use validateRequest($psr7Request)
     */
    $isValid = $validator->validatePayload($requestBody, $signature);

    if (!$isValid) {
        // Handle invalid signature
        http_response_code(400);
        exit('Invalid signature');
    }

    // Signature is valid - proceed with webhook processing

} catch (InvalidSignatureException $e) {
    // Log the invalid signature attempt for security monitoring
    error_log("Invalid webhook signature: " . $e->getMessage());
    http_response_code(400);
    exit('Invalid signature');
}
```

#### Key Rotation

During key rotation or migration periods, you can verify signatures against multiple secrets:

```php
$signingSecrets = [
    "current_secret",
    "previous_secret"  // Keep old secret during transition period
];

$validator = new SignatureValidator($signingSecrets);
$isValid = $validator->validatePayload($requestBody, $signature);
```

### Processing Webhook Payloads

Once you've verified the webhook signature, you can safely process the payload:

```php
use Mollie\Api\Webhooks\WebhookEventMapper;
use Mollie\Api\Webhooks\Events\PaymentLinkPaid;

// Process the webhook payload into an event object
$event = (new WebhookEventMapper())->processPayload($request->getParsedBody());

// Extract the entity ID (e.g., payment ID, customer ID, etc.)
$entityId = $event->entityData('id');

// Get the full resource object for direct interaction
// This only works if you subscribe to full event payloads
$resource = $event->entity()->asResource($mollie);

// Handle different event types
match (true) {
    $event instanceof PaymentLinkPaid => $this->handlePaymentLinkPaid(),
    $event instanceof BalanceTransactionCreated => $this->handleBalanceTransactionCreated(),
    // ... handle other event types
};
```

#### Using custom webhook Events
If the API is ahead of this SDK's implementation of new Events, you can create your own Events as temporary workaround and pass it into the `WebhookEventMapper`

```php
// Event class
use Mollie\Api\Webhooks\Events\BaseEvent;

class SomeEventHappened extends BaseEvent
{
    public static function type(): string
    {
        return 'some.event_happened'; // needs to match the eventType from the documentation
    }
}

// passing into event mapper and processing payload
$event = (new WebhookEventMapper([
    'some.event_happened' => SomeEventHappened::class
]))->processPayload($request->getParsedBody());
```

### Testing Webhooks

Testing webhooks is crucial to ensure your application handles all event types correctly. The SDK provides several tools to help you test webhook scenarios.

Use `MockEvent` to create realistic webhook payloads for testing:

```php
use Mollie\Api\Fake\MockEvent;
use Mollie\Api\Webhooks\Events\PaymentLinkPaid;
use Mollie\Api\Webhooks\Events\BalanceTransactionCreated;

// Create a mock PaymentLinkPaid event
$paymentLinkEventPayload = MockEvent::for(PaymentLinkPaid::class)
    ->entityId('pl_1234567890')
    ->full()  // Include full resource data
    ->create();

// Create a mock BalanceTransactionCreated event
$balanceEventPayload = MockEvent::for(BalanceTransactionCreated::class)
    ->entityId('bt_9876543210')
    ->simple()  // Webhook request without any resource data besides entityId
    ->create();
```

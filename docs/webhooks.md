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

// Process the webhook payload into an event object. Pass the signature
// (when available) so the resulting resource carries webhook provenance
// — see "Inspecting webhook provenance" below.
$event = (new WebhookEventMapper())->processPayload(
    $request->getParsedBody(),
    $signature,
);

// Extract the entity ID (e.g., payment ID, customer ID, etc.)
$entityId = $event->entityData('id');

// Hydrate the embedded entity snapshot into a fully typed SDK resource.
// No HTTP call is made — the signed webhook payload is the source of truth.
// If you subscribe to "simple" payloads (entityId only), the $event->entity
// property is null and calling $event->entity() throws. Check the property
// first, or fetch the resource yourself via its Get request using
// $event->entityId.
$resource = $event->asResource($mollie);

// Handle different event types
match (true) {
    $event instanceof PaymentLinkPaid => $this->handlePaymentLinkPaid(),
    $event instanceof BalanceTransactionCreated => $this->handleBalanceTransactionCreated(),
    // ... handle other event types
};
```

#### Binding a connector to the mapper

For application-level code, prefer constructing `WebhookEventMapper` with
the connector. Events produced by that mapper will then carry the
connector, so `asResource()` can be called with no arguments:

```php
$mapper = new WebhookEventMapper([], $mollie);

$event = $mapper->processPayload($request->getParsedBody(), $signature);

// No argument needed — the mapper's connector is used.
$resource = $event->asResource();
```

Passing an explicit connector still works and overrides the bound one on
a per-call basis:

```php
$resource = $event->asResource($otherMollie);
```

If the mapper was constructed without a connector and `asResource()` is
called with no argument, a `LogicException` is thrown pointing at the
two ways to supply one.

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

### Inspecting webhook provenance

Resources hydrated from a webhook carry a `WebhookSnapshotOrigin` instead
of an HTTP `Response`. You can inspect the event metadata without hitting
the API:

```php
$resource = $event->asResource($mollie);

$resource->getResponse();                 // null — there was no HTTP call
$resource->getOrigin();                   // WebhookSnapshotOrigin
$resource->getOrigin()->getEventId();     // 'event_GvJ8WHrp5isUdRub9CJyH'
$resource->getOrigin()->getSignature();   // the X-Mollie-Signature header, or null
$resource->getOrigin()->getReceivedAt();  // DateTimeImmutable
```

Resources fetched via the API (for example via `$mollie->send(new
GetPaymentRequest(...))`) still return a `Response` from `getResponse()`
as before.

Follow-up methods like `$payment->refunds()`, `$profile->methods()`, and
`$subscription->payments()` work the same on webhook-origin resources
as they do on HTTP-origin resources. When the embedded snapshot carries
the link URL, the SDK follows it; when it does not, the SDK routes the
request through the connector using the resource's id. Either way, the
call fires real HTTP to Mollie, which means a valid API key must be
configured on the client. A webhook handler running without an API key
is limited to reading the signed snapshot itself — there is no path
that lets the SDK reach Mollie without credentials.

### Reading from the snapshot vs. fetching fresh data

A complete handler usually combines both. The signed snapshot is enough
to make decisions. When you need data the snapshot doesn't carry
(related resources, the very latest server state), fall back to the
API. The API fallback requires a valid key on the client — the snapshot
path does not.

```php
use Mollie\Api\Exceptions\InvalidSignatureException;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Webhooks\Events\PaymentLinkPaid;
use Mollie\Api\Webhooks\SignatureValidator;
use Mollie\Api\Webhooks\WebhookEventMapper;

$mollie = new MollieApiClient;
$mollie->setApiKey(getenv('MOLLIE_API_KEY')); // only needed for the fetch path

$signingSecret = getenv('MOLLIE_WEBHOOK_SIGNING_SECRET');
$requestBody   = (string) $request->getBody();
$signature     = $request->getHeaderLine('X-Mollie-Signature');

try {
    if (! (new SignatureValidator($signingSecret))->validatePayload($requestBody, $signature)) {
        http_response_code(400);
        exit('Invalid signature');
    }
} catch (InvalidSignatureException $e) {
    error_log("Invalid webhook signature: " . $e->getMessage());
    http_response_code(400);
    exit('Invalid signature');
}

$event = (new WebhookEventMapper)->processPayload(
    json_decode($requestBody, true),
    $signature,
);

if (! $event instanceof PaymentLinkPaid) {
    // Not the event we care about — ack and return.
    http_response_code(200);
    return;
}

// 1. Snapshot path: no HTTP, no API key required.
$paymentLink = $event->asResource($mollie);

markOrderPaid(
    orderId: $paymentLink->id,
    amount:  $paymentLink->amount->value,
    paidAt:  $event->createdAt,
);

// 2. Fetch path: fires HTTP, needs the API key configured above.
//    $paymentLink->payments() follows the link from the snapshot when
//    present, otherwise routes through $mollie->paymentLinkPayments.
foreach ($paymentLink->payments() as $payment) {
    reconcilePaymentLine($paymentLink->id, $payment);
}

http_response_code(200);
```

If your webhook handler runs without an API key (signing-secret-only
deployment), remove the `setApiKey()` call and skip step 2 — you can
still do everything in step 1 from the signed payload alone.

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
    ->snapshot()  // Include full resource data
    ->create();

// Create a mock BalanceTransactionCreated event
$balanceEventPayload = MockEvent::for(BalanceTransactionCreated::class)
    ->entityId('bt_9876543210')
    ->simple()  // Webhook request without any resource data besides entityId
    ->create();
```

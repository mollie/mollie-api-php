# Webhooks

Mollie uses webhooks to notify your application about events that occur in your Mollie account. This guide explains how to work with Mollie webhooks in your application securely and efficiently.

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
     * Any validate method will throw an InvalidSignatureException if a
     * signature header is present but does not contain a valid signature
     *
     * For psr7Requests you can also use the validateRequest($psr7Request)
     */
    $isValid = $validator->validatePayload($requestBody, $signature);

    if ($isValid) {
        // Process the verified webhook event
    } else {
        // Legacy webhook without signature
    }

} catch (InvalidSignatureException $e) {
    // Log the invalid signature attempt for security monitoring
    error_log("Invalid webhook signature: " . $e->getMessage());
    http_response_code(400);
    exit('Invalid signature');
}
```

### Key Rotation

During key rotation or migration periods, you can verify signatures against multiple secrets:

```php
$signingSecrets = [
    "current_secret",
    "previous_secret"  // Keep old secret during transition period
];

$validator = new SignatureValidator($signingSecrets);
$isValid = $validator->validatePayload($requestBody, $signature);
```

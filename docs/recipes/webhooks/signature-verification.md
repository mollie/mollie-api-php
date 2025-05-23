# Webhook Signature Verification

This recipe shows you how to verify Mollie webhook signatures in your application.

```php
use Mollie\Api\MollieApiClient;
use Mollie\Api\Exceptions\InvalidSignatureException;

$mollie = new MollieApiClient();
$signingSecret = "your_webhook_signing_secret";

$request = \GuzzleHttp\Psr7\ServerRequest::fromGlobals();
$requestBody = (string)$request->getBody();
$signature = $request->getHeader("X-Mollie-Signature");

try {
    $event = $mollie->parseWebhookEvent($requestBody, $signature, $signingSecret);

    // Process the webhook event
    if ($event === \Mollie\Api\WebhookEventType::PAYMENT_PAID) {
        // Handle payment.paid event
    }

    return new \GuzzleHttp\Psr7\Response(200);
} catch (InvalidSignatureException $e) {
    return new \GuzzleHttp\Psr7\Response(400);
}
```

For more information about webhooks and signature verification, see the [Webhooks Guide](../webhooks.md).

## Advanced Usage

### Multiple Signing Secrets

During key rotation or migration periods, you can verify signatures against multiple secrets:

```php
$signingSecrets = [
    "current_secret",
    "previous_secret"
];

$event = $mollie->parseWebhookEvent(
    $requestBody,
    $signature,
    $signingSecrets
);
```

### Manual Signature Verification

If you need more control over the verification process, you can use the `SignatureValidator` directly:

```php
use Mollie\Api\Webhooks\SignatureValidator;

$validator = new SignatureValidator($signingSecret);

// Verify a PSR-7 request
$isValid = $validator->validateRequest($request);

// Or verify raw payload and signature
$isValid = $validator->validatePayload($requestBody, $signature);
```

### Legacy Webhooks

The SDK automatically handles legacy webhooks (those without signatures) by returning `false` instead of throwing an exception. This allows for a smooth transition period:

```php
if ($validator->validateRequest($request) === false) {
    // Handle legacy webhook
    return new \GuzzleHttp\Psr7\Response(200);
}
```

## Security Considerations

1. Always verify webhook signatures to ensure the request came from Mollie
2. Use HTTPS for your webhook endpoint
3. Keep your signing secrets secure and rotate them periodically
4. Consider using multiple signing secrets during key rotation
5. Return appropriate HTTP status codes:
   - 200: Successfully processed
   - 400: Invalid signature or unexpected event type
   - 500: Internal server error

## Best Practices

1. Always wrap signature verification in a try-catch block
2. Log invalid signatures for security monitoring
3. Use the SDK's built-in verification methods instead of implementing your own
4. Consider implementing a webhook queue for high-volume scenarios
5. Keep your SDK up to date to benefit from security improvements

## Troubleshooting

If you're having issues with signature verification:

1. Ensure you're using the correct signing secret from your Mollie dashboard
2. Verify that the request body hasn't been modified
3. Check that the signature header is being properly forwarded
4. Confirm that your server's time is correctly synchronized
5. Review your webhook logs for any patterns in failed verifications

# Webhooks

Mollie uses webhooks to notify your application about events that occur in your Mollie account. This guide explains how to work with Mollie webhooks in your application.

## Security

### Signature Verification

Mollie signs all webhook requests with an HMAC-SHA256 signature to ensure they come from Mollie. The signature is sent in the `X-Mollie-Signature` header.

The SDK provides built-in signature verification through the `parseWebhookEvent` method:

```php
$event = $mollie->parseWebhookEvent($requestBody, $signature, $signingSecret);
```

### Signing Secrets

Each webhook endpoint has its own signing secret. You can find your webhook signing secret in your Mollie Dashboard under:
Settings > Website profiles > Webhooks > Signing secret

### Key Rotation

During key rotation or migration periods, you can verify signatures against multiple secrets:

```php
$signingSecrets = [
    "current_secret",
    "previous_secret"
];

$event = $mollie->parseWebhookEvent($requestBody, $signature, $signingSecrets);
```

## Implementation

### Basic Implementation

The simplest way to handle webhooks is to use the SDK's `parseWebhookEvent` method:

```php
try {
    $event = $mollie->parseWebhookEvent($requestBody, $signature, $signingSecret);

    switch ($event) {
        case \Mollie\Api\WebhookEventType::PAYMENT_PAID:
            // Handle payment.paid
            break;
        case \Mollie\Api\WebhookEventType::PAYMENT_FAILED:
            // Handle payment.failed
            break;
        // ... handle other event types
    }

    return new \GuzzleHttp\Psr7\Response(200);
} catch (InvalidSignatureException $e) {
    return new \GuzzleHttp\Psr7\Response(400);
}
```

### Advanced Implementation

For more control over the verification process, you can use the `SignatureValidator` directly:

```php
use Mollie\Api\Webhooks\SignatureValidator;

$validator = new SignatureValidator($signingSecret);

// Verify a PSR-7 request
$isValid = $validator->validateRequest($request);

// Or verify raw payload and signature
$isValid = $validator->validatePayload($requestBody, $signature);
```

## Best Practices

1. **Always Verify Signatures**
   - Never process webhooks without verifying the signature
   - Use the SDK's built-in verification methods

2. **Use HTTPS**
   - Always use HTTPS for your webhook endpoint
   - Keep your SSL certificates up to date

3. **Handle Errors Gracefully**
   - Return appropriate HTTP status codes
   - Log invalid signatures for security monitoring
   - Implement proper error handling

4. **Consider Performance**
   - Process webhooks asynchronously when possible
   - Implement a webhook queue for high-volume scenarios
   - Keep processing time under 10 seconds

5. **Maintain Security**
   - Keep your signing secrets secure
   - Rotate signing secrets periodically
   - Monitor for suspicious activity

## Troubleshooting

### Common Issues

1. **Invalid Signatures**
   - Check if you're using the correct signing secret
   - Verify the request body hasn't been modified
   - Ensure the signature header is being properly forwarded

2. **Missing Webhooks**
   - Verify your webhook URL is accessible
   - Check your server's time synchronization
   - Review your webhook logs

3. **Processing Errors**
   - Implement proper error handling
   - Log all webhook processing attempts
   - Monitor your webhook queue (if used)

### Testing

You can test your webhook implementation using the Mollie Dashboard:
1. Go to Settings > Website profiles > Webhooks
2. Click "Test webhook"
3. Select an event type to test
4. Review the response and logs

## Legacy Support

The SDK automatically handles legacy webhooks (those without signatures) by returning `false` instead of throwing an exception. This allows for a smooth transition period:

```php
if ($validator->validateRequest($request) === false) {
    // Handle legacy webhook
    return new \GuzzleHttp\Psr7\Response(200);
}
```

## Related Documentation

- [Signature Verification Recipe](recipes/webhooks/signature-verification.md)
- [API Reference](https://docs.mollie.com/reference/overview/webhooks)
- [Webhook Events](https://docs.mollie.com/reference/overview/webhook-events)

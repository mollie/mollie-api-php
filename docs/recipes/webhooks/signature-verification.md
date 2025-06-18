# Webhook Signature Verification

This recipe shows you how to verify Mollie webhook signatures in your application using the `SignatureValidator` class.

## Basic Example

```php
use Mollie\Api\Webhooks\SignatureValidator;
use Mollie\Api\Exceptions\InvalidSignatureException;

$signingSecret = "foobar";

$request = \GuzzleHttp\Psr7\ServerRequest::fromGlobals();
$requestBody = (string)$request->getBody();
$signature = $request->getHeader("X-Mollie-Signature");

try {
    $validator = new SignatureValidator($signingSecret);
    $isValid = $validator->validatePayload($requestBody, $signature);

    if ($isValid) {
        // Process the verified webhook event
    } else {
        // Legacy webhook without signature
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
use Mollie\Api\Webhooks\SignatureValidator;
use Mollie\Api\Exceptions\InvalidSignatureException;

$signingSecrets = [
    "current_secret",
    "previous_secret"
];

try {
    $validator = new SignatureValidator($signingSecrets);
    $isValid = $validator->validatePayload($requestBody, $signature);

    if ($isValid) {
        processWebhook($webhookData);
    } else {
        // Handle legacy webhook
        processLegacyWebhook($webhookData);
    }

} catch (InvalidSignatureException $e) {
    http_response_code(400);
    echo 'Invalid signature';
}
```

### PSR-7 Request Validation

If you're using PSR-7 compatible requests:

```php
use Mollie\Api\Webhooks\SignatureValidator;
use Psr\Http\Message\ServerRequestInterface;

$signingSecret = "your_webhook_signing_secret";
$validator = new SignatureValidator($signingSecret);

try {
    $isValid = $validator->validateRequest($request);

    if ($isValid) {
        // Signature is valid
    } else {
        // Legacy webhook (no signature)
    }

    return new Response(200, [], 'OK');
} catch (InvalidSignatureException $e) {
    return new Response(400, [], 'Invalid signature');
}
```

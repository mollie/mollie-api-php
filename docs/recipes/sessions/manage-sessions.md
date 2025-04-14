# Manage Sessions

How to create and manage sessions using the Mollie API.

## Create a Session

```php
use Mollie\Api\Http\Requests\CreateSessionRequest;

try {
    // Create a new session
    $session = $mollie->send(
        new CreateSessionRequest([
            'paymentData' => [
                'amount' => [
                    'value' => '10.00',
                    'currency' => 'EUR'
                ],
                'description' => 'Order #12345'
            ],
            'method' => 'paypal',
            'methodDetails' => [
                'checkoutFlow' => 'express'
            ],
            'returnUrl' => 'https://example.com/shipping',
            'cancelUrl' => 'https://example.com/cancel'
        ])
    );

    // Redirect to the session URL
    header('Location: ' . $session->getRedirectUrl(), true, 303);
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## The Response

```php
$session->id;              // "ses_abc123"
$session->status;          // "created", "completed", "canceled"
$session->method;          // "paypal"
$session->methodDetails;   // Object containing method-specific details
$session->paymentData;     // Object containing payment details
$session->createdAt;       // "2024-02-24T12:13:14+00:00"
$session->expiresAt;       // "2024-02-24T13:13:14+00:00"
$session->_links;          // Object containing links (e.g., redirect URL)
```

## Additional Notes

- Sessions are used for payment methods that require additional steps
- Currently supports PayPal Express Checkout
- The session expires after 1 hour
- Use the redirect URL to send customers to complete their payment

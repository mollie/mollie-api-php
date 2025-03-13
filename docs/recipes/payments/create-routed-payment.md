# Create a Routed Payment

How to create a payment with routing rules using the Mollie API. Routed payments allow you to split payments between connected accounts.

## The Code

```php
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Data\Route;
use Mollie\Api\Http\Requests\CreatePaymentRequest;

try {
    // Initialize the Mollie client with your OAuth access token
    $mollie = new \Mollie\Api\MollieApiClient();
    $mollie->setAccessToken('access_xxx');

    // Generate a unique order ID
    $orderId = time();

    // Create the payment with routing rules
    $payment = $mollie->send(
        new CreatePaymentRequest(
            profileId: 'pfl_v9hTwCvYqw',
            description: "Order #{$orderId}",
            amount: new Money(currency: 'EUR', value: '10.00'),
            redirectUrl: 'https://example.com/return.php?order_id=' . $orderId,
            cancelUrl: 'https://example.com/cancel.php',
            webhookUrl: 'https://example.com/webhook.php',
            routing: [
                new Route(
                    amount: new Money(currency: 'EUR', value: '7.50'),
                    destination: [
                        'type' => 'organization',
                        'organizationId' => 'org_23456'
                    ]
                )
            ]
        )
    );

    // Redirect the customer to complete the payment
    header('Location: ' . $payment->getCheckoutUrl(), true, 303);
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

### With a Future Release Date

You can also specify when the routed funds should become available on the connected account's balance:

```php
$payment = $mollie->send(
    new CreatePaymentRequest(
        profileId: 'pfl_v9hTwCvYqw',
        description: "Order #{$orderId}",
        amount: new Money(currency: 'EUR', value: '10.00'),
        redirectUrl: 'https://example.com/return.php?order_id=' . $orderId,
        cancelUrl: 'https://example.com/cancel.php',
        webhookUrl: 'https://example.com/webhook.php',
        routing: [
            new Route(
                amount: new Money(currency: 'EUR', value: '7.50'),
                destination: [
                    'type' => 'organization',
                    'organizationId' => 'org_23456'
                ],
                releaseDate: '2025-01-01'
            )
        ]
    )
);
```

## The Response

```php
$payment->id;                // "tr_7UhSN1zuXS"
$payment->status;           // "open"
$payment->amount->currency; // "EUR"
$payment->amount->value;    // "10.00"
$payment->description;      // "Order #1234"
$payment->routing;          // Array containing routing rules
$payment->createdAt;        // "2024-02-24T12:13:14+00:00"
```

## Additional Notes

- Split payments (routing) must be enabled on your account first. Contact Mollie support to enable this feature
- You need an OAuth access token to create routed payments
- The sum of routed amounts cannot exceed the payment amount
- The release date must be in the future and in the format 'YYYY-MM-DD'
- Routing rules are only available for certain payment methods
- Make sure to handle the webhook to process payment status updates

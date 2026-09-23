# Create a Routed Payment

How to create a payment with routing rules using the Mollie API. Routed payments allow you to split payments between connected accounts.

## The Code

```php
use Mollie\Api\Http\Data\DataCollection;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Data\PaymentRoute;
use Mollie\Api\Http\Requests\CreatePaymentRequest;
use Mollie\Api\Types\PaymentStatus;

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
            routing: DataCollection::collect([
                new PaymentRoute(
                    amount: new Money(currency: 'EUR', value: '7.50'),
                    organizationId: 'org_23456'
                ),
            ])
        )
    );

    // Redirect the customer to complete the payment
    header('Location: ' . $payment->getCheckoutUrl(), true, 303);
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

### With a Future Release Date

You can also specify when the routed funds should become available on the connected account's balance. `delayUntil` takes a `Date` or any `DateTimeInterface`, not a raw string:

```php
use Mollie\Api\Http\Data\Date;

$payment = $mollie->send(
    new CreatePaymentRequest(
        profileId: 'pfl_v9hTwCvYqw',
        description: "Order #{$orderId}",
        amount: new Money(currency: 'EUR', value: '10.00'),
        redirectUrl: 'https://example.com/return.php?order_id=' . $orderId,
        cancelUrl: 'https://example.com/cancel.php',
        webhookUrl: 'https://example.com/webhook.php',
        routing: DataCollection::collect([
            new PaymentRoute(
                amount: new Money(currency: 'EUR', value: '7.50'),
                organizationId: 'org_23456',
                delayUntil: new Date('2025-01-01')
            ),
        ])
    )
);
```

## The Response

```php
$payment->id;                // "tr_7UhSN1zuXS"
$payment->status;           // PaymentStatus::Open, or the raw string for an unknown value
$payment->amount->currency; // "EUR"
$payment->amount->value;    // "10.00"
$payment->description;      // "Order #1234"
$payment->routing;          // array of routing rules (or null)
$payment->createdAt;        // "2024-02-24T12:13:14+00:00"
```

`status` is `PaymentStatus|string`, so normalise it before printing or storing it:

```php
$status = $payment->status instanceof PaymentStatus
    ? $payment->status->value
    : $payment->status;
```

## Additional Notes

- Pass routes as a `DataCollection` of `Mollie\Api\Http\Data\PaymentRoute` objects; each route names the destination organization through `organizationId`
- Split payments (routing) must be enabled on your account first. Contact Mollie support to enable this feature
- You need an OAuth access token to create routed payments
- The sum of routed amounts cannot exceed the payment amount
- `delayUntil` must be in the future; pass a `Date` (which formats as 'YYYY-MM-DD') or a `DateTimeInterface`
- Routing rules are only available for certain payment methods
- Make sure to handle the webhook to process payment status updates

# Create a Payment

How to prepare a new payment with the Mollie API.

## The Code

```php
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CreatePaymentRequest;

try {
    // Return type is Payment, inferred via send()'s @template — no @var needed
    $payment = $mollie->send(
        new CreatePaymentRequest(
            description: "Order #{$orderId}",
            amount: Money::euro('10.00'),
            // or: amount: Money::fromMinorUnits('EUR', 1000),
            redirectUrl: 'https://example.org/return',
            cancelUrl: 'https://example.org/cancel',
            webhookUrl: 'https://example.org/webhook',
            metadata: ['order_id' => $orderId]
        )
    );

    // Get the checkout URL to redirect the customer to
    $checkoutUrl = $payment->getCheckoutUrl();
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## The Response

```php
$payment->id;                // "tr_7UhSN1zuXS"
$payment->status;           // PaymentStatus::Open (enum) | "open" string fallback for unknown values
$payment->amount;           // Mollie\Api\Http\Data\Money (readonly value object)
$payment->amount->currency; // "EUR"
$payment->amount->value;    // "10.00"
$payment->description;      // "Order #1234"
$payment->metadata->order_id; // "1234"
$payment->getCheckoutUrl(); // "https://www.mollie.com/checkout/select-method/7UhSN1zuXS"
```

Compare statuses against the enum cases (recommended) or string values:

```php
use Mollie\Api\Types\PaymentStatus;

if ($payment->status === PaymentStatus::Paid) {
    // ...
}
```

## Additional Notes

- Always use strings for the amount value to ensure correct decimal precision
- The payment `status` will be `open` when initially created
- Store the payment `id` in your database for reference
- Use `webhookUrl` to get notified of payment status changes
- The `redirectUrl` is where your customer will be redirected after the payment
- You can store any custom data in the `metadata` object
- The checkout URL from `getCheckoutUrl()` is where you should redirect your customer to complete the payment

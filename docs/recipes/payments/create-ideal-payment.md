# Create an iDEAL Payment

How to prepare a new iDEAL payment with the Mollie API.

## The Code

```php
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CreatePaymentRequest;
use Mollie\Api\Types\PaymentMethod;
use Mollie\Api\Types\PaymentStatus;

try {
    // Generate a unique order ID
    $orderId = time();

    // Create the payment
    $payment = $mollie->send(
        new CreatePaymentRequest(
            description: "Order #{$orderId}",
            amount: Money::euro('27.50'),  // Using convenience method
            redirectUrl: "https://example.com/return.php?order_id={$orderId}",
            cancelUrl: "https://example.com/cancel.php",
            webhookUrl: "https://example.com/webhook.php",
            metadata: ['order_id' => $orderId],
            method: PaymentMethod::Ideal
        )
    );

    // PaymentStatus case for known values, raw string for anything newer.
    // Store the string: an enum case cannot be written to the database and
    // interpolating one throws.
    $status = $payment->status instanceof PaymentStatus
        ? $payment->status->value
        : $payment->status;

    // Store the order in the database
    database_write($orderId, $status);

    // Redirect to checkout
    header('Location: ' . $payment->getCheckoutUrl(), true, 303);
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo 'API call failed: ' . htmlspecialchars($e->getMessage());
}
```

## The Response

```php
$payment->id;                // "tr_7UhSN1zuXS"
$payment->status;           // PaymentStatus::Open, or the raw string for an unknown value
$payment->amount->currency; // "EUR"
$payment->amount->value;    // "27.50"
$payment->description;      // "Order #1234"
$payment->metadata->order_id; // "1234"
$payment->method;          // PaymentMethod::Ideal, a raw string, or null
$payment->getCheckoutUrl(); // "https://www.mollie.com/checkout/select-method/7UhSN1zuXS"
```

## Additional Notes

- `status` is `PaymentStatus|string` and `method` is `PaymentMethod|string|null`; normalise with an `instanceof` check before printing or persisting
- The iDEAL payment method is only available for payments in EUR
- The bank selection is now handled by iDEAL
- The webhook will be called when the payment status changes, so make sure to implement the webhook handler to process status updates

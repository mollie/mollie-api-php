# Update a Payment

How to update an existing payment using the Mollie API.

## The Code

```php
use Mollie\Api\Http\Requests\GetPaymentRequest;
use Mollie\Api\Http\Requests\UpdatePaymentRequest;

try {
    // First retrieve the payment you want to update
    $payment = $mollie->send(
        new GetPaymentRequest(
            id: 'tr_7UhSN1zuXS'
        )
    );

    // Update specific payment fields
    $newOrderId = 98765;
    $payment = $mollie->send(
        new UpdatePaymentRequest(
            id: $payment->id,
            description: "Order #{$newOrderId}",
            redirectUrl: 'https://example.com/return.php?order_id=' . $newOrderId,
            metadata: ['order_id' => $newOrderId]
            // Fields we don't specify will keep their current values:
            // - webhookUrl
            // - cancelUrl
            // - etc.
        )
    );

    // Redirect the customer to complete the payment
    header('Location: ' . $payment->getCheckoutUrl(), true, 303);
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## The Response

```php
$payment->id;                // "tr_7UhSN1zuXS"
$payment->status;           // PaymentStatus::Open, or the raw string for an unknown value
$payment->description;      // "Order #98765"
$payment->redirectUrl;      // "https://example.com/return.php?order_id=98765" (or null)
$payment->webhookUrl;       // "https://example.com/webhook.php" (or null)
$payment->metadata;         // Object containing order_id
$payment->createdAt;        // "2024-02-24T12:13:14+00:00" (or null)
```

`status` is `PaymentStatus|string`, so normalise it before printing or storing it:

```php
use Mollie\Api\Types\PaymentStatus;

$status = $payment->status instanceof PaymentStatus
    ? $payment->status->value
    : $payment->status;
```

## Additional Notes

- Only certain fields can be updated, including:
  - description
  - redirectUrl
  - cancelUrl
  - webhookUrl
  - metadata
- You cannot update the amount or currency of a payment
- The payment must be in a state that allows updates (e.g., you cannot update a completed payment)
- Make sure to handle the webhook to process payment status updates
- `Payment` has no `updatedAt` property; the response returns the updated resource, so re-read the fields you changed

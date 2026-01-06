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
$payment->status;           // "open"
$payment->description;      // "Order #98765"
$payment->redirectUrl;      // "https://example.com/return.php?order_id=98765"
$payment->webhookUrl;       // "https://example.com/webhook.php"
$payment->metadata;         // Object containing order_id
$payment->createdAt;        // "2024-02-24T12:13:14+00:00"
$payment->updatedAt;        // "2024-02-24T12:15:00+00:00"
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
- The updatedAt field will be set to the time of the last update

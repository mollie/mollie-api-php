# Create a Payment Capture

How to capture a payment that was created with manual capture using the Mollie API.

## The Code

```php
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Resources\Capture;
use Mollie\Api\Http\Requests\CreatePaymentCaptureRequest;

try {
    // Create a capture for the payment
    /** @var Capture */
    $capture = $mollie->send(
        new CreatePaymentCaptureRequest(
            paymentId: 'tr_WDqYK6vllg',
            description: 'Order #12345',
            amount: new Money(currency: 'EUR', value: '5.00')
        )
    );

    echo "New capture created: {$capture->id}\n";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## The Response

```php
$capture->id;                // "cpt_4qqhO89gsT"
$capture->paymentId;        // "tr_WDqYK6vllg"
$capture->amount->currency; // "EUR"
$capture->amount->value;    // "5.00"
$capture->description;      // "Order #12345"
$capture->status;          // "pending", "succeeded", "failed"
$capture->createdAt;       // "2024-02-24T12:13:14+00:00"
```

## Additional Notes

- The payment must have been created with `captureMode: 'manual'`
- The capture amount can be equal to or lower than the payment amount
- You can create multiple partial captures for a single payment
- The capture will be processed asynchronously
- The payment status will change to `paid` once the capture is successful
- Make sure to handle the webhook to process capture status updates
- Captures are only available for certain payment methods (e.g., credit cards)

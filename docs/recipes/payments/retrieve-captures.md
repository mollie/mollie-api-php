# Retrieve Payment Captures

How to retrieve and list captures for a payment using the Mollie API.

## Get a Single Capture

```php
use Mollie\Api\Resources\Capture;
use Mollie\Api\Http\Requests\GetPaymentCaptureRequest;

try {
    // Retrieve a specific capture
    /** @var Capture $capture */
    $capture = $mollie->send(
        new GetPaymentCaptureRequest(
            paymentId: 'tr_WDqYK6vllg',
            captureId: 'cpt_4qqhO89gsT'
        )
    );

    // Capture::$amount is nullable per the API contract, so guard the deref.
    $amount = $capture->amount;

    echo $amount === null
        ? "Capture {$capture->id} has no amount\n"
        : "Captured {$amount->currency} {$amount->value}\n";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## List All Captures

```php
use Mollie\Api\Resources\Capture;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Http\Requests\GetPaginatedPaymentCapturesRequest;

try {
    // List all captures for a payment
    /** @var LazyCollection $captures */
    $captures = $mollie->send(
        (new GetPaginatedPaymentCapturesRequest(
            paymentId: 'tr_WDqYK6vllg'
        ))->useIterator()
    );

    /** @var Capture $capture */
    foreach ($captures as $capture) {
        $amount = $capture->amount;

        echo "Capture {$capture->id}:\n";
        echo '- Amount: '.($amount === null ? 'not available' : "{$amount->currency} {$amount->value}")."\n";
        echo '- Status: '.($capture->status ?? 'unknown')."\n";
        echo '- Created: '.($capture->createdAt ?? 'unknown')."\n\n";
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## The Response

```php
$capture->id;                // "cpt_4qqhO89gsT"
$capture->paymentId;        // "tr_WDqYK6vllg"
$capture->amount;           // Money, or null when the API omits it
$capture->amount?->currency; // "EUR"
$capture->amount?->value;    // "5.00"
$capture->description;      // "Order #12345" (or null)
$capture->status;          // "pending", "succeeded", "failed" (or null)
$capture->createdAt;       // "2024-02-24T12:13:14+00:00" (or null)
```

`Capture::$amount` is nullable per the API contract, so guard it (or use `?->`)
before reading `currency` or `value`. `status` is a plain nullable string here,
not a backed enum.

## Additional Notes

- You can only retrieve captures for payments created with `captureMode: 'manual'`
- A payment can have multiple captures if partial captures were used
- The captures list is paginated, use `next()` to get the next page
- The capture status indicates whether the capture was successful
- The payment status will change to `paid` once all captures are successful
- Make sure to handle the webhook to process capture status updates

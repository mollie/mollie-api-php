# List Payments

How to retrieve a list of payments from the Mollie API.

## The Code

```php
use Mollie\Api\Http\Requests\GetPaginatedPaymentsRequest;

try {
    // Use the lazy iterator to walk every page transparently — no manual next() loop
    foreach ($mollie->payments->iterator() as $payment) {
        echo "Payment {$payment->id}:\n";
        echo "- Description: {$payment->description}\n";
        echo "- Amount: {$payment->amount->currency} {$payment->amount->value}\n";
        echo "- Status: {$payment->status->value}\n";

        if ($payment->hasRefunds()) {
            echo "- Has been (partially) refunded\n";
        }

        if ($payment->hasChargebacks()) {
            echo "- Has been charged back\n";
        }

        if ($payment->canBeRefunded() && $payment->amountRemaining->currency === 'EUR' && floatval($payment->amountRemaining->value) >= 2.00) {
            echo "- Can be refunded\n";
        }

        echo "\n";
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## The Response

```php
$payment->id;                // "tr_7UhSN1zuXS"
$payment->status;           // "paid"
$payment->amount->currency; // "EUR"
$payment->amount->value;    // "10.00"
$payment->description;      // "Order #1234"
$payment->metadata;         // Object containing any custom metadata
$payment->createdAt;        // "2024-02-24T12:13:14+00:00"
$payment->paidAt;          // "2024-02-24T12:15:00+00:00" (or null)
$payment->method;          // "ideal" (or null)
```

## Additional Notes

- Payments are returned in descending order by creation date (newest first)
- `iterator()` lazily fetches each page on demand — only one page is held in memory at a time
- For manual page control, send a `GetPaginatedPaymentsRequest` and call `->next()` on the returned cursor collection
- You can check payment status using helper methods like `isPaid()`, `isFailed()`, etc., or compare against `PaymentStatus::Paid`
- Use `hasRefunds()` and `hasChargebacks()` to check if a payment has been refunded or charged back
- Use `canBeRefunded()` to check if a payment can still be refunded

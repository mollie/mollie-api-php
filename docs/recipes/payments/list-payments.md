# List Payments

How to retrieve a list of payments from the Mollie API.

## The Code

```php
use Mollie\Api\Http\Requests\GetPaginatedPaymentsRequest;

try {
    // Get all payments for this API key ordered by newest
    $payments = $mollie->send(new GetPaginatedPaymentsRequest);

    // Display the payments
    foreach ($payments as $payment) {
        echo "Payment {$payment->id}:\n";
        echo "- Description: {$payment->description}\n";
        echo "- Amount: {$payment->amount->currency} {$payment->amount->value}\n";
        echo "- Status: {$payment->status}\n";
        
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

    // Get the next page of payments if available
    $nextPayments = $payments->next();
    
    if (!empty($nextPayments)) {
        foreach ($nextPayments as $payment) {
            // Process next page of payments...
        }
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
- The list is paginated, use the `next()` method to retrieve the next page
- You can check payment status using helper methods like `isPaid()`, `isFailed()`, etc.
- Use `hasRefunds()` and `hasChargebacks()` to check if a payment has been refunded or charged back
- Use `canBeRefunded()` to check if a payment can still be refunded

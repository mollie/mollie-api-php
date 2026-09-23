# Refund a Payment

How to refund a payment using the Mollie API.

## The Code

```php
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\GetPaymentRequest;
use Mollie\Api\Http\Requests\CreatePaymentRefundRequest;
use Mollie\Api\Types\RefundStatus;

try {
    // Retrieve the payment you want to refund
    $payment = $mollie->send(
        new GetPaymentRequest(
            id: 'tr_WDqYK6vllg'
        )
    );

    // Check if the payment can be refunded
    if ($payment->canBeRefunded() && $payment->amountRemaining->currency === 'EUR' && floatval($payment->amountRemaining->value) >= 2.00) {
        // Refund € 2,00 of the payment
        // Note: Using named parameters to avoid PHP 8+ deprecation warnings
        // Alternative: Use factory method CreatePaymentRefundRequest::for($paymentId, Money::euro('2.00'), 'Description')
        $refund = $mollie->send(
            new CreatePaymentRefundRequest(
                paymentId: $payment->id,
                amount: Money::euro('2.00'),
                description: 'Order cancelled by customer'
            )
        );

        echo "{$refund->amount->currency} {$refund->amount->value} of payment {$payment->id} refunded.\n";
    } else {
        echo "Payment {$payment->id} cannot be refunded.\n";
    }

    // List all refunds for this payment
    foreach ($payment->refunds() as $refund) {
        // status is RefundStatus|string|null: an enum case when this release
        // knows the value, the raw string when Mollie ships a new one.
        // Interpolating the property directly throws on an enum case.
        $status = $refund->status instanceof RefundStatus
            ? $refund->status->value
            : $refund->status ?? 'unknown';

        echo "Refund {$refund->id}:\n";
        echo '- Description: '.($refund->description ?? 'no description')."\n";
        echo "- Amount: {$refund->amount->currency} {$refund->amount->value}\n";
        echo "- Status: {$status}\n\n";
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## The Response

```php
$refund->id;                // "re_4qqhO89gsT"
$refund->amount->currency; // "EUR"
$refund->amount->value;    // "2.00"
$refund->status;          // RefundStatus case, raw string, or null
$refund->description;     // "Order cancelled by customer" (or null)
$refund->createdAt;       // "2024-02-24T12:13:14+00:00"
$refund->paymentId;       // "tr_WDqYK6vllg"
```

## Additional Notes

- Not all payments can be refunded. Use `canBeRefunded()` to check if a payment can be refunded
- You can do partial refunds by specifying a lower amount than the payment amount
- Refunds are not instant. Check the refund status to see if it was successful
- The payment must be in the `paid` status to be refundable
- Some payment methods may have additional requirements or limitations for refunds
- `RefundStatus` covers `Queued`, `Pending`, `Processing`, `Refunded`, `Failed` and `Canceled`. Compare with `isPending()`, `isProcessing()`, `isFailed()` or `Utility::equals()` so an unrecognised string from a newer API version still behaves predictably

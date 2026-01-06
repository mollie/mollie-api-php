# Refund a Payment

How to refund a payment using the Mollie API.

## The Code

```php
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\GetPaymentRequest;
use Mollie\Api\Http\Requests\CreatePaymentRefundRequest;

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
        $refund = $mollie->send(
            new CreatePaymentRefundRequest(
                paymentId: $payment->id,
                description: 'Order cancelled by customer',
                amount: new Money(currency: 'EUR', value: '2.00')
            )
        );

        echo "{$refund->amount->currency} {$refund->amount->value} of payment {$payment->id} refunded.\n";
    } else {
        echo "Payment {$payment->id} cannot be refunded.\n";
    }

    // List all refunds for this payment
    foreach ($payment->refunds() as $refund) {
        echo "Refund {$refund->id}:\n";
        echo "- Description: {$refund->description}\n";
        echo "- Amount: {$refund->amount->currency} {$refund->amount->value}\n";
        echo "- Status: {$refund->status}\n\n";
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
$refund->status;          // "pending", "processing", "refunded", "failed"
$refund->description;     // "Order cancelled by customer"
$refund->createdAt;       // "2024-02-24T12:13:14+00:00"
$refund->paymentId;       // "tr_WDqYK6vllg"
```

## Additional Notes

- Not all payments can be refunded. Use `canBeRefunded()` to check if a payment can be refunded
- You can do partial refunds by specifying a lower amount than the payment amount
- Refunds are not instant. Check the refund status to see if it was successful
- The payment must be in the `paid` status to be refundable
- Some payment methods may have additional requirements or limitations for refunds

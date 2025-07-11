# Customer Payments

How to create and manage payments for customers using the Mollie API.

## First Payment (For Recurring)

```php
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CreateCustomerPaymentRequest;
use Mollie\Api\Types\SequenceType;

try {
    // Create the first payment for a customer
    $payment = $mollie->send(
        new CreateCustomerPaymentRequest(
            customerId: 'cst_8wmqcHMN4U',
            description: 'First payment - Order #12345',
            amount: new Money(
                currency: 'EUR',
                value: '29.95'
            ),
            redirectUrl: 'https://example.com/payments/return?order_id=12345',
            webhookUrl: 'https://example.com/payments/webhook',
            metadata: [
                'order_id' => '12345'
            ],
            sequenceType: SequenceType::FIRST // This creates a mandate for future payments
        )
    );

    // Redirect the customer to complete the payment
    header('Location: ' . $payment->getCheckoutUrl(), true, 303);
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## Recurring Payment

```php
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CreateCustomerPaymentRequest;
use Mollie\Api\Types\SequenceType;

try {
    // Create a recurring payment using the mandate
    $payment = $mollie->send(
        new CreateCustomerPaymentRequest(
            customerId: 'cst_8wmqcHMN4U',
            description: 'Recurring payment - Order #12346',
            amount: new Money(
                currency: 'EUR',
                value: '29.95'
            ),
            webhookUrl: 'https://example.com/payments/webhook',
            metadata: [
                'order_id' => '12346'
            ],
            sequenceType: SequenceType::RECURRING // This uses the mandate created by the first payment
        )
    );

    // The payment will be either pending or paid immediately
    echo "Payment status: {$payment->status}\n";
    echo "Used mandate: {$payment->mandateId}\n";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## List Customer Payments

```php
use Mollie\Api\Http\Requests\GetPaginatedCustomerPaymentsRequest;
use Mollie\Api\Resources\PaymentCollection;

try {
    // Get all payments for a customer
    /** @var PaymentCollection */
    $payments = $mollie->send(
        new GetPaginatedCustomerPaymentsRequest(
            customerId: 'cst_8wmqcHMN4U'
        )
    );

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

        echo "\n";
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## The Payment Response

```php
$payment->id;                // "tr_7UhSN1zuXS"
$payment->customerId;        // "cst_8wmqcHMN4U"
$payment->mode;             // "live" or "test"
$payment->description;      // "Order #12345"
$payment->metadata;         // Object containing custom metadata
$payment->status;           // "open", "pending", "paid", "failed", "expired", "canceled"
$payment->isCancelable;     // Whether the payment can be canceled
$payment->sequenceType;     // "first" or "recurring"
$payment->redirectUrl;      // URL to redirect the customer to
$payment->webhookUrl;       // URL for webhook notifications
$payment->createdAt;        // "2024-02-24T12:13:14+00:00"
```

## Additional Notes

- First create a customer before creating customer payments
- The first payment creates a mandate for future recurring payments
- Sequence types:
  - `first`: Creates a mandate for future payments
  - `recurring`: Uses an existing mandate
- Recurring payments:
  - Don't need a `redirectUrl` as they use the stored mandate
  - Will be executed immediately without customer interaction
  - May still be pending depending on the payment method
- The mandate status determines if recurring payments can be created
- Store the payment ID and status in your database
- Always implement proper webhook handling
- Available payment methods may differ for first and recurring payments
- Some payment methods don't support recurring payments

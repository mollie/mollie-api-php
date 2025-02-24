# Handle Payment Webhooks

How to handle payment status updates via webhooks from the Mollie API.

## The Code

```php
use Mollie\Api\Http\Requests\GetPaymentRequest;

try {
    // Retrieve the payment's current state
    $payment = $mollie->send(
        new GetPaymentRequest(
            id: $_POST['id']
        )
    );
    $orderId = $payment->metadata->order_id;

    

    // First handle status changes
    if ($payment->status !== $previousPaymentStatus) {
        // Update your order administration with the new status
        updateOrder($orderId, $payment->status);

        // Handle the status change
        if ($payment->isPaid()) {
            // The payment is paid
            // Start the process of delivering the product to the customer
            startDeliveryProcess($orderId);
        } elseif ($payment->isFailed()) {
            // The payment has failed
            // Notify the customer
            notifyCustomerOfFailure($orderId);
        } elseif ($payment->isExpired()) {
            // The payment is expired
            // Notify the customer
            notifyCustomerOfExpiration($orderId);
        } elseif ($payment->isCanceled()) {
            // The payment is canceled
            // Notify the customer
            notifyCustomerOfCancellation($orderId);
        }
    }

    // Then handle refunds and chargebacks (these don't change the payment status)
    if ($payment->hasRefunds()) {
        // The payment has possibly been (partially) refunded
        // Note: the payment status remains "paid"
        processPotentialRefund($orderId);
    }
    
    if ($payment->hasChargebacks()) {
        // The payment has possibly been (partially) charged back
        // Note: the payment status remains "paid"
        processPotentialChargeback($orderId);
    }

    // Always return 200 OK to Mollie
    http_response_code(200);
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    // Handle the error
    logError($e->getMessage());
    http_response_code(500);
}
```

## The Response

```php
$payment->id;                // "tr_7UhSN1zuXS"
$payment->status;           // "paid"
$payment->amount->currency; // "EUR"
$payment->amount->value;    // "10.00"
$payment->description;      // "Order #1234"
$payment->metadata;         // Object containing order_id
$payment->createdAt;        // "2024-02-24T12:13:14+00:00"
$payment->paidAt;          // "2024-02-24T12:15:00+00:00"
```

## Additional Notes

- Handle webhooks in the correct order:
  1. First check for payment status changes
  2. Then handle refunds and chargebacks (these don't change the payment status).
- When completing a payment, the webhook may be called _after_ the customer has already been redirected to the payment's `redirectUrl`. 
- A payment can have multiple partial refunds and multiple partial chargebacks.
- Always respond with a 200 OK status code, even when the payment status is not successful
- Process the webhook quickly (within 2 seconds) to avoid timeouts
- Use the payment status helper methods (`$payment->isPaid()`, `$payment->isFailed()`, etc.) for reliable status checks
- Store the payment status in your database to track the order status
- The webhook may be called multiple times for the same payment as Mollie will retry the webhook call.
- Webhooks are sent asynchronously, so there might be a delay
- Test your webhook implementation thoroughly with different payment scenarios, including:
  - Status changes (paid, failed, expired, etc.)
  - Refunds (full and partial)
  - Chargebacks


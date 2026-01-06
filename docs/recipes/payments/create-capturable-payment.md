# Create a Capturable Payment

How to create a payment that can be captured manually with the Mollie API.

## The Code

```php
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CreatePaymentRequest;
use Mollie\Api\Types\PaymentMethod;

try {
    // Generate a unique order ID
    $orderId = time();

    // Create the payment
    $payment = $mollie->send(
        new CreatePaymentRequest(
            description: "Order #{$orderId}",
            amount: new Money(currency: 'EUR', value: '10.00'),
            redirectUrl: "https://example.com/return.php?order_id={$orderId}",
            cancelUrl: "https://example.com/cancel.php",
            webhookUrl: "https://example.com/webhook.php",
            metadata: ['order_id' => $orderId],
            method: PaymentMethod::CREDITCARD,
            captureMode: 'manual'
        )
    );

    // Store the order in the database
    database_write($orderId, $payment->status);

    // Redirect to checkout
    header('Location: ' . $payment->getCheckoutUrl(), true, 303);
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo 'API call failed: ' . htmlspecialchars($e->getMessage());
}
```

## The Response

```php
$payment->id;                // "tr_7UhSN1zuXS"
$payment->status;           // "open"
$payment->amount->currency; // "EUR"
$payment->amount->value;    // "10.00"
$payment->description;      // "Order #1234"
$payment->metadata->order_id; // "1234"
$payment->method;          // "creditcard"
$payment->captureMode;     // "manual"
$payment->getCheckoutUrl(); // "https://www.mollie.com/checkout/select-method/7UhSN1zuXS"
```

## Additional Notes

- Manual capturing is currently only supported for credit card payments, Billie, Riverty, and Klarna
- For Klarna payments, submitting more data like order lines and addresses is required
- After the payment is authorized, you'll need to capture it manually to actually charge the customer
- The payment will remain in the 'authorized' status until you either capture or cancel it
- Make sure to implement the webhook handler to process payment status updates

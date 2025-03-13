# Manage Payment Links

How to create and list payment links using the Mollie API.

## Create a Payment Link

```php
use Mollie\Api\Http\Requests\CreatePaymentLinkRequest;

try {
    // Create a payment link
    $paymentLink = $mollie->send(
        new CreatePaymentLinkRequest([
            'amount' => [
                'currency' => 'EUR',
                'value' => '10.00'
            ],
            'description' => 'Bicycle tires',
            'expiresAt' => '2024-12-31', // optional
            'webhookUrl' => 'https://example.com/webhook' // optional
        ])
    );

    // Redirect the customer to the payment link
    header('Location: ' . $paymentLink->getCheckoutUrl(), true, 303);
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## List Payment Links

```php
use Mollie\Api\Http\Requests\ListPaymentLinksRequest;

try {
    // List all payment links
    $response = $mollie->send(
        new ListPaymentLinksRequest()
    );

    foreach ($response as $paymentLink) {
        echo "Payment Link {$paymentLink->id}:\n";
        echo "- Description: {$paymentLink->description}\n";
        echo "- Amount: {$paymentLink->amount->currency} {$paymentLink->amount->value}\n";
        echo "- Status: {$paymentLink->status}\n";
        echo "- Created: {$paymentLink->createdAt}\n";
        echo "- URL: {$paymentLink->getCheckoutUrl()}\n\n";
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## The Response

```php
$paymentLink->id;           // "pl_4Y0eZitmBnQ6IDoMqZQKh"
$paymentLink->description;  // "Bicycle tires"
$paymentLink->amount;       // Object containing amount and currency
$paymentLink->status;       // "paid", "open", "expired"
$paymentLink->createdAt;    // "2024-02-24T12:13:14+00:00"
$paymentLink->paidAt;       // "2024-02-24T12:15:16+00:00" (optional)
$paymentLink->expiresAt;    // "2024-12-31" (optional)
$paymentLink->webhookUrl;   // "https://example.com/webhook" (optional)
```

## Additional Notes

- Payment links are shareable URLs to accept payments
- They never expire unless you specify an `expiresAt` date
- The webhook will be called when the payment status changes
- Payment links can be shared via email, chat, or QR code

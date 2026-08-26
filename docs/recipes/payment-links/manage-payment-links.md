# Manage Payment Links

How to create and list payment links using the Mollie API.

## Create a Payment Link

```php
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CreatePaymentLinkRequest;

try {
    // Create a payment link. The request takes typed arguments, not an array.
    $paymentLink = $mollie->send(
        new CreatePaymentLinkRequest(
            description: 'Bicycle tires',
            amount: Money::euro('10.00'),
            webhookUrl: 'https://example.com/webhook', // optional
            expiresAt: '2026-01-01T12:00:00' // optional
        )
    );

    // getCheckoutUrl() reads $_links->paymentLink and returns null when that
    // link is absent, so check before redirecting.
    $checkoutUrl = $paymentLink->getCheckoutUrl();

    if ($checkoutUrl === null) {
        throw new \RuntimeException("Payment link {$paymentLink->id} has no checkout URL.");
    }

    header('Location: ' . $checkoutUrl, true, 303);
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## List Payment Links

```php
use Mollie\Api\Http\Requests\GetPaginatedPaymentLinksRequest;

try {
    // List all payment links
    $response = $mollie->send(new GetPaginatedPaymentLinksRequest);

    foreach ($response as $paymentLink) {
        // PaymentLink has no status field. Derive the state from paidAt,
        // expiresAt and archived instead.
        $amount = $paymentLink->amount;

        echo "Payment Link {$paymentLink->id}:\n";
        echo "- Description: {$paymentLink->description}\n";
        echo '- Amount: '.($amount === null ? 'open amount' : "{$amount->currency} {$amount->value}")."\n";
        echo '- Paid: '.($paymentLink->isPaid() ? 'yes' : 'no')."\n";
        echo '- Archived: '.($paymentLink->archived ? 'yes' : 'no')."\n";
        echo '- Created: '.($paymentLink->createdAt ?? 'unknown')."\n";
        echo '- URL: '.($paymentLink->getCheckoutUrl() ?? 'not available')."\n\n";
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## The Response

```php
$paymentLink->id;           // "pl_4Y0eZitmBnQ6IDoMqZQKh"
$paymentLink->description;  // "Bicycle tires"
$paymentLink->amount;       // Mollie\Api\Http\Data\Money (null for an open amount)
$paymentLink->archived;     // false
$paymentLink->createdAt;    // "2024-02-24T12:13:14.0Z" (or null)
$paymentLink->paidAt;       // "2024-02-24T12:15:16.0Z" (or null)
$paymentLink->expiresAt;    // "2026-01-01T00:00:00.0Z" (or null)
$paymentLink->webhookUrl;   // "https://example.com/webhook" (or null)
$paymentLink->isPaid();     // true once paidAt is set
```

There is no `$paymentLink->status`. Use `isPaid()`, `expiresAt` and `archived`
to determine the state of a payment link.

## Additional Notes

- Payment links are shareable URLs to accept payments
- Omit `amount` to create an open-amount link; `amount` is then `null` on the response
- They never expire unless you specify an `expiresAt` date
- The webhook will be called when the payment status changes
- Payment links can be shared via email, chat, or QR code

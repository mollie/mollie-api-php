# Manage Checkout Sessions

How to create and retrieve Checkout Sessions using the Mollie API.

> Checkout Sessions are in private beta at Mollie. The API specification may still change.

## Create a Checkout Session

```php
try {
    $session = $mollie->sessions->create([
        'amount' => [
            'currency' => 'EUR',
            'value' => '10.00',
        ],
        'description' => 'Order #12345',
        'redirectUrl' => 'https://example.com/order/12345',
        'lines' => [
            [
                'description' => 'Bicycle tire',
                'quantity' => 1,
                'unitPrice' => [
                    'currency' => 'EUR',
                    'value' => '10.00',
                ],
                'totalAmount' => [
                    'currency' => 'EUR',
                    'value' => '10.00',
                ],
            ],
        ],
        'payment' => [
            'webhookUrl' => 'https://example.com/webhook',
        ],
        'requiredCustomerDetails' => ['email', 'shipping-address'],
    ]);

    // Hand the client access token to Mollie's client-side components
    // to render the checkout in your frontend.
    $clientAccessToken = $session->clientAccessToken;
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

You can also send the typed request directly:

```php
use Mollie\Api\Http\Data\DataCollection;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Data\OrderLine;
use Mollie\Api\Http\Requests\CreateSessionRequest;

$session = $mollie->send(
    new CreateSessionRequest(
        Money::euro('10.00'),
        'Order #12345',
        'https://example.com/order/12345',
        DataCollection::collect([
            new OrderLine('Bicycle tire', 1, Money::euro('10.00'), Money::euro('10.00')),
        ])
    )
);
```

## Retrieve a Checkout Session

```php
$session = $mollie->sessions->get('sess_CQBQJqxubaq4w6oresxMJ');
```

## The Response

```php
$session->id;                      // "sess_CQBQJqxubaq4w6oresxMJ"
$session->mode;                    // "live" or "test"
$session->status;                  // "open", "completed" or "expired"
$session->clientAccessToken;       // token for the client-side checkout
$session->amount;                  // Object with currency and value
$session->description;             // "Order #12345"
$session->redirectUrl;             // "https://example.com/order/12345"
$session->lines;                   // Array of line items (or null)
$session->requiredCustomerDetails; // ["email", "shipping-address"] (or null)
$session->billingAddress;          // Object with address details (or null)
$session->shippingAddress;         // Object with address details (or null)
$session->customerId;              // "cst_5B8cwPMGnU" (or null)
$session->sequenceType;            // "oneoff" or "first" (or null)
$session->metadata;                // Your own data (or null)
$session->payment;                 // Object with payment settings, e.g. webhookUrl (or null)
$session->profileId;               // "pfl_5B8cwPMGnU"
$session->createdAt;               // "2026-09-30T12:00:00+00:00"
$session->expiredAt;               // Set once the session expired (or null)
$session->completedAt;             // Set once the session completed (or null)

$session->isOpen();
$session->isCompleted();
$session->isExpired();
```

## Additional Notes

- `amount`, `description`, `redirectUrl` and `lines` are required
- `requiredCustomerDetails` accepts `email`, `billing-address` and `shipping-address`; Mollie returns the collected details on `billingAddress` and `shippingAddress`
- `cancelUrl` is deprecated: the Checkout Sessions API does not accept a cancel URL

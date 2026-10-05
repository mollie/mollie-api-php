# Manage Checkout Sessions

How to create and retrieve Checkout Sessions using the Mollie API.

> Checkout Sessions are in private beta at Mollie. The API specification may still change.

## Create a Checkout Session

```php
use Mollie\Api\Http\Data\DataCollection;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Data\OrderLine;
use Mollie\Api\Http\Requests\CreateSessionRequest;

try {
    // The request takes typed arguments, not an array.
    $session = $mollie->send(
        new CreateSessionRequest(
            amount: Money::euro('10.00'),
            description: 'Order #12345',
            redirectUrl: 'https://example.com/order/12345',
            lines: DataCollection::collect([
                new OrderLine(
                    description: 'Bicycle tire',
                    quantity: 1,
                    unitPrice: Money::euro('10.00'),
                    totalAmount: Money::euro('10.00')
                ),
            ]),
            paymentWebhook: 'https://example.com/webhook',
            requiredCustomerDetails: ['email', 'shipping-address'],
        )
    );

    // Hand the client access token to Mollie's client-side components
    // to render the checkout in your frontend.
    $clientAccessToken = $session->clientAccessToken;
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## Retrieve a Checkout Session

```php
use Mollie\Api\Http\Requests\GetSessionRequest;

$session = $mollie->send(new GetSessionRequest('sess_CQBQJqxubaq4w6oresxMJ'));
```

## The Response

```php
$session->id;                      // "sess_CQBQJqxubaq4w6oresxMJ"
$session->mode;                    // "live" or "test"
$session->status;                  // SessionStatus case or raw string
$session->clientAccessToken;       // token for the client-side checkout
$session->amount;                  // Mollie\Api\Http\Data\Money
$session->description;             // "Order #12345"
$session->redirectUrl;             // "https://example.com/order/12345"
$session->lines;                   // array of line items (or null)
$session->requiredCustomerDetails; // ["email", "shipping-address"] (or null)
$session->billingAddress;          // Mollie\Api\Http\Data\Address (or null)
$session->shippingAddress;         // Mollie\Api\Http\Data\Address (or null)
$session->customerId;              // "cst_5B8cwPMGnU" (or null)
$session->sequenceType;            // "oneoff" or "first" (or null)
$session->metadata;                // your own data (or null)
$session->payment;                 // \stdClass with payment settings, e.g. webhookUrl (or null)
$session->profileId;               // "pfl_5B8cwPMGnU"
$session->createdAt;               // "2026-09-30T12:00:00+00:00"
$session->expiredAt;               // set once the session expired (or null)
$session->completedAt;             // set once the session completed (or null)
```

`status` is a `SessionStatus` case (`Open`, `Expired`, `Completed`) when the SDK
recognises the value and the raw string otherwise. Read it through the helpers
rather than interpolating it:

```php
use Mollie\Api\Types\SessionStatus;

if ($session->isOpen()) {
    // still awaiting the customer
}

$status = $session->status instanceof SessionStatus
    ? $session->status->value
    : $session->status;

echo "Session status: {$status}\n";
```

## Additional Notes

- `amount`, `description`, `redirectUrl` and `lines` are required; `lines` takes a `DataCollection` of `OrderLine` objects
- Pass `paymentWebhook` to receive status updates; the SDK nests it as `payment.webhookUrl`
- `requiredCustomerDetails` accepts `email`, `billing-address` and `shipping-address`; Mollie returns the collected details on `billingAddress` and `shippingAddress`
- `cancelUrl` and `getRedirectUrl()` are deprecated: the Checkout Sessions API accepts no cancel URL and only returns a `self` link
- Use `isOpen()`, `isExpired()` and `isCompleted()` to branch on the session status

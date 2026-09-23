# Manage Sessions

How to create and manage sessions using the Mollie API.

## Create a Session

```php
use Mollie\Api\Http\Data\DataCollection;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Data\OrderLine;
use Mollie\Api\Http\Requests\CreateSessionRequest;

try {
    // Create a new session. The request takes typed arguments, not an array.
    $session = $mollie->send(
        new CreateSessionRequest(
            amount: Money::euro('10.00'),
            description: 'Order #12345',
            redirectUrl: 'https://example.com/shipping',
            lines: DataCollection::collect([
                new OrderLine(
                    description: 'Bicycle tire',
                    quantity: 1,
                    unitPrice: Money::euro('10.00'),
                    totalAmount: Money::euro('10.00')
                ),
            ]),
            cancelUrl: 'https://example.com/cancel',
            paymentWebhook: 'https://example.com/webhook'
        )
    );

    // getRedirectUrl() reads $_links->redirect and returns null when the API
    // did not send that link, so check before redirecting.
    $redirectUrl = $session->getRedirectUrl();

    if ($redirectUrl === null) {
        throw new \RuntimeException("Session {$session->id} has no redirect link.");
    }

    header('Location: ' . $redirectUrl, true, 303);
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## The Response

```php
$session->id;                // "ses_abc123"
$session->status;            // SessionStatus case or raw string
$session->amount;            // Mollie\Api\Http\Data\Money
$session->description;       // "Order #12345"
$session->redirectUrl;       // "https://example.com/shipping"
$session->cancelUrl;         // "https://example.com/cancel" (or null)
$session->clientAccessToken; // token for the client-side checkout
$session->lines;             // array of order lines (or null)
$session->metadata;          // your own data (or null)
$session->payment;           // \stdClass with payment settings, e.g. webhookUrl (or null)
$session->_links;            // \stdClass with HAL links, e.g. redirect
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

- Sessions are used for payment methods that require additional steps
- `amount`, `description`, `redirectUrl` and `lines` are required; `lines` takes a `DataCollection` of `OrderLine` objects
- Pass `paymentWebhook` to receive status updates; the SDK nests it as `payment.webhookUrl`
- `getRedirectUrl()` is nullable, so guard it before issuing a redirect
- Use `isOpen()`, `isExpired()` and `isCompleted()` to branch on the session status

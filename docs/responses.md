# Responses

Whether you interact with the endpoints using the traditional method (`$mollie->payments->...`) or the new `Request` classes, you can always inspect the raw `Response`.

## Resource Hydration
By default, all responses from are automatically hydrated into the corresponding `Resource` or `ResourceCollection` objects. You can still access the raw response using the `->getResponse()` method.

For example, when retrieving a payment you'll receive a Payment resource object, on which you can still access the raw Response class.

```php
/**
 * Legacy approach
 *
 * @var Mollie\Api\Resources\Payment $payment
 */
$payment = $mollie->payments->get('tr_*********');

/**
 * New approach
 *
 * @var Mollie\Api\Resources\Payment $payment
 */
$payment = $mollie->send(new GetPaymentRequest('tr_*********'));

$response = $payment->getResponse();
```

`getResponse()` returns `?Response`. Resources produced by an API call always
carry a non-null `Response`. Resources hydrated from a signed webhook snapshot
return `null` (there was no HTTP call). Null-check before chaining, or use
`getOrigin()` for origin-agnostic metadata — see [`docs/webhooks.md`](webhooks.md).

## Resource Wrappers
Sometimes it's useful to hydrate a class of your own with the information returned by the API. A wrapper can expose a subset of the resource's properties or cast them into your own objects, while retaining access to the underlying `Resource` it wraps.

### Define a Wrapper

```php
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\ResourceWrapper;

class PaymentWrapper extends ResourceWrapper
{
    public function __construct(
        public Money $amount,
        public ?\DateTimeImmutable $createdAt,
    ) {
    }

    public static function fromResource($resource): self
    {
        /** @var Payment $resource */
        return (new self(
            amount: $resource->amount,
            createdAt: $resource->createdAt === null
                ? null
                : new \DateTimeImmutable($resource->createdAt),
        ))->setWrapped($resource);
    }
}
```

### Usage

Call `wrapInto()` on the request. `send()` then returns your wrapper, and PHPStan infers its type without a `@var` annotation:

```php
use Mollie\Api\Http\Requests\GetPaymentRequest;

$paymentWrapper = $mollie->send(
    (new GetPaymentRequest('tr_*********'))->wrapInto(PaymentWrapper::class)
);
// $paymentWrapper is PaymentWrapper
```

PHPStan currently honors both the `@phpstan-self-out` and `@psalm-this-out` annotations, and the committed fixture guards their combined inference contract. This repository does not run Psalm, so Psalm behavior is not verified here. PhpStorm does not infer the narrowed type.

To hydrate into a different SDK resource class first, call `hydrateInto()` before `wrapInto()`:

```php
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedPaymentRefundsRequest;
use Mollie\Api\Resources\RefundCollection;

// Payment::$_links is ?stdClass, and a payment only carries a `refunds` link
// when the API includes one — a payment with no refunds has neither. Guard
// both hops in one isset(), the same way Payment::refunds() does internally.
if (! isset($payment->_links->refunds->href)) {
    // Fall back to the paginated endpoint, which routes by payment id. Keep
    // the same hydrateInto()/wrapInto() chain as the linked branch so both
    // branches hand back a RefundsWrapper, not a bare RefundCollection.
    $refunds = $mollie->send(
        (new GetPaginatedPaymentRefundsRequest(paymentId: $payment->id))
            ->hydrateInto(RefundCollection::class)
            ->wrapInto(RefundsWrapper::class)
    );
} else {
    $refunds = $mollie->send(
        (new DynamicGetRequest($payment->_links->refunds->href))
            ->hydrateInto(RefundCollection::class)
            ->wrapInto(RefundsWrapper::class)
    );
}
```

`RefundsWrapper` is an application wrapper implementing `IsWrapper`, for example by extending `ResourceWrapper` as above. Call request-specific setters first, then `hydrateInto()`, then `wrapInto()` last. Reversing the two named helpers makes static analysis report the hydration target while runtime still returns the wrapper.

`setHydratableResource(new WrapperResource(PaymentWrapper::class))` still works, but it keeps the request's original generic type, so `send()` cannot infer the wrapper.

The original `Payment` resource properties and methods can be accessed through the wrapper class.

```php
// access property
$paymentWrapper->status;

// access method
$paymentWrapper->isPaid();
```

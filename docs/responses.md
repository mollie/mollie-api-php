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

## Resource Wrappers
Sometimes it's benefitial to directly hydrate a custom class with the information returned by the API. The wrapper resource can be used to define a subset of resource properties used by your app or cast them into your own dedicated Objects.

The resource wrapper class still has access to the underlying `Resource` it wrapps around.

### Define a Wrapper

```php
use Mollie\Api\Utils\Utility;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\ResourceWrapper;

class PaymentWrapper extends ResourceWrapper
{
    public function __construct(
        public Money $amount,
        public Timestamp $createdAt,
    )

    public static function fromResource($resource): self
    {
        /** @var Payment $resource */
        return (new self(
            amount: Utility::transform($resource->amount, fn (stdClass $amount) => Money::fromMollieObject($amount))
            createdAt: Utility::transform($resource->createdAt, fn (string $timestamp) => Timestamp::fromIsoString($timestamp))
        ))->setWrapped($resource);
    }
}
```

The `Utility::transform()` method can be used to transform values into your own objects.


### Usage

A resource wrapper can be used by setting the `setHydratableResource()` to the new `WrapperResource`.

```php
use Mollie\Api\Resources\WrapperResource;

$request = new GetPaymentRequest('tr_*********');

$request->setHydratableResource(new WrapperResource(PaymentWrapper::class));

/** @var PaymentWrapper $paymentWrapper */
$paymentWrapper = $mollie->send($request);
```

The original `Payment` resource properties and methods can be accessed through the wrapper class.

```php
// access property
$paymentWrapper->status;

// access method
$paymentWrapper->status();
```

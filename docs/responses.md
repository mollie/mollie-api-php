# Responses

Whether you interact with the endpoints using the traditional method (`$mollie->payments->...`) or the new `Request` classes, you can always inspect the `Response`.

## Resource Hydration
By default, the response from the `EndpointCollection`s automatically hydrates into the corresponding `Resource` or `ResourceCollection` objects. You can still access the raw response using the `->getResponse()` method.

```php
/** @var Mollie\Api\Resources\Payment $payment */
$payment = $mollie->payments->get('tr_*********');

$response = $payment->getResponse();
```

With the Request-based approach, you get a Response by default:

```php
/** @var Mollie\Api\Http\Response $response */
$response = $mollie->send(new GetPaymentRequest('tr_*********'));

/**
 * Accessing the response is mainly for debugging,
 * like checking the status or inspecting the payload or URL.
 */
$status = $response->status();
$sentPayload = $response->getPendingRequest()->payload;
$sentUrlWithFilters = $response->getPendingRequest()->getUri();

/** @var Mollie\Api\Resources\Payment $payment */
$payment = $response->toResource();
```

Thanks to the DelegatesToResource Trait in Response, you can still access methods and attributes from the underlying Resource:

```php
// calling a method on the underlying Mollie\Api\Resources\Payment object
$response->hasSplitPayments();

// accessing an attribute on the underlying Mollie\Api\Resources\Payment object
$amount = $response->amount;
```

If you prefer the old approach of directly receiving the Resource class, you can enable **auto-hydration** by calling `MollieApiClient::setAutoHydrate()`.

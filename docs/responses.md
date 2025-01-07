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

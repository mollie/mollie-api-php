# Requests

## Overview

The Mollie API client uses request classes to communicate with the Mollie API. Each request class handles specific API endpoints and operations. The response is casted into a dedicated `Mollie\Api\Resources\*` class.

For a complete list of available request classes, see the [Request Reference](request-reference.md).

## Sending a Request

To send a request using the Mollie API client, you typically need to:

1. **Create an instance of the client**:
   ```php
   use Mollie\Api\MollieApiClient;

   $mollie = new MollieApiClient();
   $mollie->setApiKey('test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM');
   ```

2. **Create and configure the request**:
   Depending on the operation, you might need to create an instance of a specific request class and configure it with necessary parameters.

3. **Send the request**:
   Use the client to send the request and handle the response.

## Best Practices: Using Named Parameters (PHP 8.0+)

**Recommended**: Use named parameters for cleaner, more readable code. This approach makes it clear what each parameter represents and avoids issues with optional parameters:

```php
use Mollie\Api\MollieApiClient;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CreatePaymentRequest;

$mollie = new MollieApiClient();
$mollie->setApiKey('test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');

// Recommended: Use named parameters (PHP 8.0+)
$createPaymentRequest = new CreatePaymentRequest(
    description: 'Test payment',
    amount: Money::euro('10.00'),  // Using convenience method
    redirectUrl: 'https://example.org/redirect',
    webhookUrl: 'https://example.org/webhook',
    locale: 'en_US',
    metadata: ['order_id' => '123']
);

/** @var \Mollie\Api\Resources\Payment $payment */
$payment = $mollie->send($createPaymentRequest);
```

**Legacy**: Positional parameters still work but are less readable, especially with many optional parameters:

```php
// Legacy: Positional parameters (PHP 7.4)
$createPaymentRequest = new CreatePaymentRequest(
    'Test payment',
    new Money('EUR', '10.00'),
    'https://example.org/redirect',
    null,  // cancelUrl
    'https://example.org/webhook'
    // ... many nulls for optional parameters
);
```

## Money Object Convenience Methods

Creating `Money` objects is now easier with convenience methods:

```php
use Mollie\Api\Http\Data\Money;

// Using convenience methods
$amount = Money::euro('10.00');
$amount = Money::usd('10.00');

// Parse from string
$amount = Money::fromString('EUR 10.00');  // Parses "EUR 10.00" or "10.00 EUR"

// Traditional constructor still works
$amount = new Money('EUR', '10.00');
```

## Adding unsupported properties
If the SDK is not up to date with the API, you can manually add a property to a request via the `query()` or `payload()` methods.

```php
$someRequestUsingPayload = new SomePayloadRequest(...);
$someRequestUsingPayload->payload()->add('foo', 'bar');

$someRequestUsingQueryParams = new SomeQueryRequest(...);
$someRequestUsingQueryParams->query()->add('foo', 'bar');
```

## Parameter Ordering Notes

Some request classes have optional parameters before required parameters in their constructors. This can trigger PHP 8+ deprecation warnings when using positional arguments. To avoid this:

1. **Use named parameters** (recommended for PHP 8.0+):
   ```php
   use Mollie\Api\Http\Requests\CreatePaymentRefundRequest;
   use Mollie\Api\Http\Data\Money;

   // Using named parameters avoids deprecation warnings
   $request = new CreatePaymentRefundRequest(
       paymentId: $paymentId,
       amount: Money::euro('10.00'),
       description: 'Refund description'
   );
   ```

2. **Use factory methods** where available:
   ```php
   // CreatePaymentRefundRequest has a factory method with correct parameter order
   $request = CreatePaymentRefundRequest::for(
       $paymentId,
       Money::euro('10.00'),
       'Refund description'
   );
   ```

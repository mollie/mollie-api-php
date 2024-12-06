# Testing with Mollie API Client

## Overview

Version 3 of the Mollie API client refines the handling of the test mode parameter:

- **Automatic Removal of Test Mode**: When using an API key, the test mode parameter is managed based on the key prefix (`test_` or `live_`).
- **Explicit Test Mode Control**: For operations requiring explicit control, such as when using OAuth tokens, you can still pass the `testmode` parameter.

## Enabling Test Mode

### Global Test Mode

You can enable test mode globally on the Mollie client. This setting will apply test mode to all operations performed with the client.

```php
use Mollie\Api\MollieApiClient;

$mollie = new MollieApiClient();
$mollie->setApiKey("test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM");
$mollie->test(true); // Enable test mode globally
```

### Per Request Test Mode

For specific control, you can enable test mode per request. This is useful for mixed-mode operations.

```php
// Creating a payment in test mode
use Mollie\Api\MollieApiClient;
use Mollie\Api\Http\Payload\Money;
use Mollie\Api\Http\Payload\CreatePaymentPayload;
use Mollie\Api\Http\Requests\CreatePaymentRequest;

$mollie = new MollieApiClient();
$createPaymentRequest = new CreatePaymentRequest(
    new CreatePaymentPayload(
        'Test payment',
        new Money('EUR', '10.00'),
        'https://example.org/redirect',
        'https://example.org/webhook'
    )
);

$mollie->send($createPaymentRequest->test(true));
```

### Using Test Mode with Endpoint Collections

When using endpoint collections, pass the test mode parameter directly to methods that support it.

```php
// Fetch a customer in test mode
$customer = $mollie->customers->get('cust_12345678', testmode: true);
```

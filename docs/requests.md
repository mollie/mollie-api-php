# Requests

## Overview

The Mollie API client uses request classes to communicate with the Mollie API. Each request class handles specific API endpoints and operations.

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
   ```php
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

   /** @var \Mollie\Api\Http\Response $response */
   $response = $mollie->send($createPaymentRequest);

   $this->assertEquals(200, $response->status());

   /** @var \Mollie\Api\Resources\Payment */
   $payment = $response->toResource();
   ```
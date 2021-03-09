<?php

namespace Tests\Mollie\API\Exceptions;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Exceptions\ApiException;
use PHPUnit\Framework\TestCase;
use Tests\Mollie\TestHelpers\LinkObjectTestHelpers;

class ApiExceptionTest extends TestCase
{
    use LinkObjectTestHelpers;

    public function testCanGetRequestBodyIfRequestIsSet()
    {
        $response = new Response(
            422,
            [],
            /** @lang JSON */
            '{
                    "status": 422,
                    "title": "Unprocessable Entity",
                    "detail": "Can not enable Credit card via the API. Please go to the dashboard to enable this payment method.",
                    "_links": {
                         "dashboard": {
                                "href": "https://www.mollie.com/dashboard/settings/profiles/pfl_v9hTwCvYqw/payment-methods",
                                "type": "text/html"
                         },
                         "documentation": {
                                "href": "https://docs.mollie.com/guides/handling-errors",
                                "type": "text/html"
                         }
                    }
                }'
        );

        $request = new Request(
            'POST',
            'https://api.mollie.com/v2/profiles/pfl_v9hTwCvYqw/methods/bancontact',
            [],
            /** @lang JSON */
            '{ "foo": "bar" }'
        );

        $exception = ApiException::createFromResponse($response, $request);

        $this->assertJsonStringEqualsJsonString(/** @lang JSON */'{ "foo": "bar" }', $exception->getRequest()->getBody()->__toString());
        $this->assertStringEndsWith('Error executing API call (422: Unprocessable Entity): Can not enable Credit card via the API. Please go to the dashboard to enable this payment method.. Documentation: https://docs.mollie.com/guides/handling-errors. Request body: { "foo": "bar" }', $exception->getMessage());
    }
}

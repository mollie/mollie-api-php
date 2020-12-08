<?php

namespace Tests\Mollie\API\Exceptions;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Mollie\Api\Exceptions\ApiException;
use PHPUnit\Framework\TestCase;
use Tests\Mollie\TestHelpers\LinkObjectTestHelpers;

class ApiExceptionTest extends TestCase
{
    use LinkObjectTestHelpers;

    public function testCreateFromGuzzleException()
    {
        $response = new Response(
            422,
            [],
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

        $guzzleException = new RequestException(
            'Something went wrong...',
            new Request(
                'POST',
                'https://api.mollie.com/v2/profiles/pfl_v9hTwCvYqw/methods/bancontact',
                [],
                '{ "foo": "bar" }'
            ),
            $response
        );

        $exception = ApiException::createFromGuzzleException($guzzleException);

        $this->assertInstanceOf(ApiException::class, $exception);
        $this->assertInstanceOf(Response::class, $exception->getResponse());

        $this->assertEquals($response, $exception->getResponse());
        $this->assertTrue($exception->hasResponse());

        $this->assertTrue($exception->hasLink('dashboard'));
        $this->assertTrue($exception->hasLink('documentation'));
        $this->assertFalse($exception->hasLink('foo'));

        $this->assertLinkObject(
            'https://www.mollie.com/dashboard/settings/profiles/pfl_v9hTwCvYqw/payment-methods',
            'text/html',
            $exception->getLink('dashboard')
        );

        $this->assertEquals(
            'https://www.mollie.com/dashboard/settings/profiles/pfl_v9hTwCvYqw/payment-methods',
            $exception->getUrl('dashboard')
        );

        $this->assertEquals(
            'https://www.mollie.com/dashboard/settings/profiles/pfl_v9hTwCvYqw/payment-methods',
            $exception->getDashboardUrl()
        );

        $this->assertLinkObject(
            'https://docs.mollie.com/guides/handling-errors',
            'text/html',
            $exception->getLink('documentation')
        );

        $this->assertEquals(
            'https://docs.mollie.com/guides/handling-errors',
            $exception->getDocumentationUrl()
        );

        $this->assertNull($exception->getLink('foo'));
        $this->assertNull($exception->getUrl('foo'));

        $this->assertInstanceOf(\DateTimeInterface::class, $exception->getRaisedAt());

        $this->assertNull($exception->getRequest());
    }

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

        $guzzleException = new RequestException(
            'Something went wrong...',
            $request,
            $response
        );

        $exception = ApiException::createFromGuzzleException($guzzleException, $request);

        $this->assertJsonStringEqualsJsonString(/** @lang JSON */'{ "foo": "bar" }', $exception->getRequest()->getBody()->__toString());
        $this->assertStringEndsWith('Error executing API call (422: Unprocessable Entity): Can not enable Credit card via the API. Please go to the dashboard to enable this payment method.. Documentation: https://docs.mollie.com/guides/handling-errors. Request body: { "foo": "bar" }', $exception->getMessage());
    }
}

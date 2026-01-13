<?php

namespace Tests\Http\Adapter;

use Mollie\Api\Exceptions\NotFoundException;
use Mollie\Api\Exceptions\RetryableNetworkRequestException;
use Mollie\Api\Fake\FakeResponseLoader;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\LinearRetryStrategy;
use Mollie\Api\Http\Requests\CreatePaymentRequest;
use Mollie\Api\Http\Requests\GetPaymentRequest;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Payment;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class SymfonyMollieHttpAdapterTest extends TestCase
{
    private const TEST_API_KEY = 'test_some_api_key_aaaaaaaaaaaaaaaaa';

    /** @test */
    public function sends_get_request(): void
    {
        $httpClient = new MockHttpClient(static function (string $method, string $url, array $options): ResponseInterface {
            self::assertSame('GET', $method);
            self::assertSame('https://api.mollie.com/v2/payments/some_id?embed=captures%2Crefunds%2Cchargebacks', $url);

            self::assertContains('Authorization: Bearer test_some_api_key_aaaaaaaaaaaaaaaaa', $options['headers']);
            self::assertContains('Accept: application/json', $options['headers']);

            return self::createFakePaymentResponse();
        });

        $mollie = new MollieApiClient($httpClient);
        $mollie->setApiKey(self::TEST_API_KEY);
        $payment = $mollie->send(new GetPaymentRequest(
            'some_id',
            true,
            true,
            true,
        ));

        self::assertInstanceOf(Payment::class, $payment);
        self::assertSame('some_id', $payment->id);
    }

    /** @test */
    public function sends_post_request(): void
    {
        $httpClient = new MockHttpClient(static function (string $method, string $url, array $options): ResponseInterface {
            self::assertSame('POST', $method);
            self::assertSame('https://api.mollie.com/v2/payments', $url);

            self::assertContains('Authorization: Bearer test_some_api_key_aaaaaaaaaaaaaaaaa', $options['headers']);
            self::assertContains('Accept: application/json', $options['headers']);
            self::assertContains('Content-Type: application/json', $options['headers']);

            self::assertJsonStringEqualsJsonString(
                '{"description":"My Payment","amount":{"currency":"EUR","value":"123.45"}}',
                $options['body'],
            );

            return self::createFakePaymentResponse();
        });

        $mollie = new MollieApiClient($httpClient);
        $mollie->setApiKey(self::TEST_API_KEY);
        $payment = $mollie->send(new CreatePaymentRequest(
            'My Payment',
            new Money('EUR', '123.45'),
        ));

        self::assertInstanceOf(Payment::class, $payment);
        self::assertSame('some_id', $payment->id);
    }

    /** @test */
    public function handles_404_correctly(): void
    {
        $httpClient = new MockHttpClient(static fn () => new MockResponse(
            <<< 'JSON'
                {
                  "status": "404",
                  "title": "Not Found",
                  "detail": "No resource found",
                  "field": "",
                  "_links": {
                    "documentation": {
                      "href": "https://docs.mollie.com/overview/handling-errors",
                      "type": "text/html"
                    }
                  }
                }
                JSON,
            [
                'http_code' => 404,
                'response_headers' => ['Content-Type' => 'application/json'],
            ],
        ));

        $mollie = new MollieApiClient($httpClient);
        $mollie->setApiKey(self::TEST_API_KEY);

        $this->expectException(NotFoundException::class);
        $mollie->payments->get('some_id');
    }

    /** @test */
    public function handles_transport_exceptions_correctly(): void
    {
        $response = $this->createStub(ResponseInterface::class);
        $response->method('getStatusCode')->willThrowException(
            new TransportException('Something went wrong'),
        );

        $httpClient = new MockHttpClient([$response]);

        $mollie = new MollieApiClient($httpClient);
        $mollie->setApiKey(self::TEST_API_KEY);
        $mollie->setRetryStrategy(new LinearRetryStrategy(0, 0));

        $this->expectException(RetryableNetworkRequestException::class);
        $mollie->payments->get('some_id');
    }

    /** @test */
    public function supports_retry(): void
    {
        $response = $this->createStub(ResponseInterface::class);
        $response->method('getStatusCode')->willThrowException(
            new TransportException('Something went wrong'),
        );

        $httpClient = new MockHttpClient([
            $response,
            self::createFakePaymentResponse(),
        ]);

        $mollie = new MollieApiClient($httpClient);
        $mollie->setApiKey(self::TEST_API_KEY);
        $mollie->setRetryStrategy(new LinearRetryStrategy(1, 0));

        $payment = $mollie->payments->get('some_id');

        self::assertInstanceOf(Payment::class, $payment);
        self::assertSame('some_id', $payment->id);
    }

    private static function createFakePaymentResponse(): MockResponse
    {
        return new MockResponse(
            str_replace('{{ RESOURCE_ID }}', 'some_id', FakeResponseLoader::load('payment')),
            [
                'response_headers' => ['Content-Type' => 'application/json'],
            ],
        );
    }
}

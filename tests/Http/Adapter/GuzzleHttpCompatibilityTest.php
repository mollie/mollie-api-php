<?php

declare(strict_types=1);

namespace Tests\Http\Adapter;

use Composer\CaBundle\CaBundle;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException as GuzzleRequestException;
use GuzzleHttp\Exception\TooManyRedirectsException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request as PsrRequest;
use GuzzleHttp\Psr7\Response as PsrResponse;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Exceptions\NetworkRequestException;
use Mollie\Api\Exceptions\RetryableNetworkRequestException;
use Mollie\Api\Exceptions\TooManyRequestsException;
use Mollie\Api\Exceptions\ValidationException;
use Mollie\Api\Http\Adapter\GuzzleMollieHttpAdapter;
use Mollie\Api\Http\Adapter\PSR18MollieHttpAdapter;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\ExponentialRetryStrategy;
use Mollie\Api\Http\LinearRetryStrategy;
use Mollie\Api\Http\Requests\CreatePaymentRequest;
use Mollie\Api\Http\Requests\GetPaymentRequest;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Payment;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;
use ReflectionProperty;
use RuntimeException;

class GuzzleHttpCompatibilityTest extends TestCase
{
    public function testNormalResponseHydratesWithEitherHttpErrorsSetting(): void
    {
        foreach ([true, false] as $httpErrors) {
            $history = [];
            [$client, $handler] = $this->client([$this->payment()], $history, false, ['http_errors' => $httpErrors]);

            $payment = $client->send(new GetPaymentRequest('tr_offline'));

            $this->assertInstanceOf(Payment::class, $payment);
            $this->assertSame('tr_offline', $payment->id);
            $this->assertSame(0, $handler->count());
            $this->assertCount(1, $history);
        }
    }

    public function testValidationResponseRetainsItsFieldAndSenderException(): void
    {
        foreach ([true, false] as $httpErrors) {
            $history = [];
            [$client, $handler] = $this->client([
                $this->error(422, 'amount'), $this->payment(),
            ], $history, false, ['http_errors' => $httpErrors]);

            try {
                $client->send(new GetPaymentRequest('tr_offline'));
                $this->fail('Expected validation failure.');
            } catch (ValidationException $e) {
                $this->assertSame(422, $e->getStatusCode());
                $this->assertSame('amount', $e->getField());
                $this->assertSame($httpErrors, $e->getResponse()->getSenderException() instanceof GuzzleRequestException);
            }

            $this->assertSame(1, $handler->count());
            $this->assertCount(1, $history);
        }
    }

    public function testResponseLessRequestFailureIsWrappedWithoutRetrying(): void
    {
        foreach ([false, true] as $psr18) {
            $failure = new GuzzleRequestException('Invalid offline request', $this->request());
            $history = [];
            [$client, $handler] = $this->client([$failure, $this->payment()], $history, $psr18);
            $client->clearIdempotencyKeyGenerator();
            $client->setIdempotencyKey('offline-key');
            $fatalCount = 0;
            $client->middleware()->onFatal(function () use (&$fatalCount) {
                $fatalCount++;
            });

            try {
                $client->send(new CreatePaymentRequest('offline', new Money('EUR', '10.00')));
                $this->fail('Expected request failure.');
            } catch (NetworkRequestException $e) {
                $this->assertNotInstanceOf(RetryableNetworkRequestException::class, $e);
                $this->assertSame($failure, $e->getPrevious());
                $this->assertSame('Invalid offline request', $e->getPlainMessage());
            }

            $this->assertSame(1, $handler->count());
            $this->assertCount(1, $history);
            $this->assertSame(1, $fatalCount);
            $this->assertNull($client->getIdempotencyKey());
            $client->send(new CreatePaymentRequest('next', new Money('EUR', '10.00')));
            $this->assertFalse($history[1]['request']->hasHeader('Idempotency-Key'));
        }
    }

    public function testOptionalResponseMethodMustReturnAPsrResponse(): void
    {
        foreach ([false, true] as $psr18) {
            $failure = new class extends RuntimeException implements RequestExceptionInterface {
                public function getRequest(): RequestInterface
                {
                    return new PsrRequest('GET', 'https://example.invalid');
                }

                public function getResponse(): string
                {
                    return 'not a response';
                }
            };
            $history = [];
            [$client, $handler] = $this->client([$failure, $this->payment()], $history, $psr18);

            try {
                $client->send(new GetPaymentRequest('tr_offline'));
                $this->fail('Expected request failure.');
            } catch (NetworkRequestException $e) {
                $this->assertNotInstanceOf(RetryableNetworkRequestException::class, $e);
                $this->assertSame($failure, $e->getPrevious());
            }

            $this->assertSame(1, $handler->count());
            $this->assertCount(1, $history);
        }
    }

    public function testInaccessibleResponseAccessorsPreserveTheOriginalFailure(): void
    {
        $failures = [
            new class extends RuntimeException implements RequestExceptionInterface {
                public function getRequest(): RequestInterface
                {
                    return new PsrRequest('GET', 'https://example.invalid');
                }

                public function response(): PsrResponse
                {
                    return $this->getResponse();
                }

                private function getResponse(): PsrResponse
                {
                    return new PsrResponse(422);
                }
            },
            new class extends RuntimeException implements RequestExceptionInterface {
                public function getRequest(): RequestInterface
                {
                    return new PsrRequest('GET', 'https://example.invalid');
                }

                public function response(): PsrResponse
                {
                    return $this->getResponse();
                }

                protected function getResponse(): PsrResponse
                {
                    return new PsrResponse(422);
                }
            },
        ];

        foreach ($failures as $failure) {
            $this->assertSame(422, $failure->response()->getStatusCode());
            foreach ([false, true] as $psr18) {
                $this->assertPermanentFailure($failure, $psr18);
            }
        }
    }

    public function testNetworkFailuresRetryWithTheSameBodyAndIdempotencyKey(): void
    {
        foreach ($this->networkExceptionClasses() as $exceptionClass) {
            foreach ([false, true] as $psr18) {
                $failure = new $exceptionClass('Offline network failure', $this->request());
                $history = [];
                [$client, $handler] = $this->client([$failure, $this->payment()], $history, $psr18);
                $client->setIdempotencyKey('offline-retry-key');

                $payment = $client->send(new CreatePaymentRequest('offline', new Money('EUR', '10.00')));

                $this->assertInstanceOf(Payment::class, $payment);
                $this->assertSame(0, $handler->count());
                $this->assertCount(2, $history);
                $this->assertSame('offline-retry-key', $history[0]['request']->getHeaderLine('Idempotency-Key'));
                $this->assertSame('offline-retry-key', $history[1]['request']->getHeaderLine('Idempotency-Key'));
                $this->assertSame((string) $history[0]['request']->getBody(), (string) $history[1]['request']->getBody());
                $this->assertNull($client->getIdempotencyKey());
            }
        }
    }

    public function testNetworkFailuresExhaustTheConfiguredRetriesAndRunFatalHooksOnce(): void
    {
        foreach ($this->networkExceptionClasses() as $exceptionClass) {
            foreach ([false, true] as $psr18) {
                $failure = new $exceptionClass('Offline network failure', $this->request());
                $history = [];
                [$client, $handler] = $this->client([$failure, $failure, $failure, $this->payment()], $history, $psr18);
                $client->setRetryStrategy(new LinearRetryStrategy(2, 0));
                $client->setIdempotencyKey('offline-exhausted-key');
                $fatalCount = 0;
                $client->middleware()->onFatal(function () use (&$fatalCount) {
                    $fatalCount++;
                });

                try {
                    $client->send(new CreatePaymentRequest('offline', new Money('EUR', '10.00')));
                    $this->fail('Expected exhausted network retries.');
                } catch (RetryableNetworkRequestException $e) {
                    $this->assertSame($failure, $e->getPrevious());
                }

                $this->assertSame(1, $handler->count());
                $this->assertCount(3, $history);
                foreach ($history as $attempt) {
                    $this->assertSame('offline-exhausted-key', $attempt['request']->getHeaderLine('Idempotency-Key'));
                }
                $this->assertSame(1, $fatalCount);
                $this->assertNull($client->getIdempotencyKey());
            }
        }
    }

    public function testZeroRetriesSendsNetworkFailureOnlyOnce(): void
    {
        $failure = new ConnectException('Offline connect failure', $this->request());
        $history = [];
        [$client, $handler] = $this->client([$failure, $this->payment()], $history);
        $client->setRetryStrategy(new LinearRetryStrategy(0, 0));

        try {
            $client->send(new GetPaymentRequest('tr_offline'));
            $this->fail('Expected network failure.');
        } catch (RetryableNetworkRequestException $e) {
            $this->assertSame($failure, $e->getPrevious());
        }

        $this->assertSame(1, $handler->count());
        $this->assertCount(1, $history);
    }

    public function testLinearPolicyDoesNotRetryRateLimits(): void
    {
        $history = [];
        [$client, $handler] = $this->client([$this->error(429), $this->payment()], $history);

        try {
            $client->send(new GetPaymentRequest('tr_offline'));
            $this->fail('Expected rate-limit failure.');
        } catch (TooManyRequestsException $e) {
            $this->assertSame(429, $e->getStatusCode());
        }

        $this->assertSame(1, $handler->count());
        $this->assertCount(1, $history);
    }

    public function testExponentialPolicyRetriesRateLimits(): void
    {
        $history = [];
        [$client, $handler] = $this->client([$this->error(429)->withHeader('Retry-After', '0'), $this->payment()], $history);
        $client->setRetryStrategy(new ExponentialRetryStrategy(1, 0, 2.0, 0, false));

        $this->assertInstanceOf(Payment::class, $client->send(new GetPaymentRequest('tr_offline')));
        $this->assertSame(0, $handler->count());
        $this->assertCount(2, $history);
    }

    public function testFailedPartialResponsesNeverHydrateOrRetryEvenWithAnErrorStatus(): void
    {
        foreach ([$this->payment(), $this->error(422), $this->error(429)] as $response) {
            // Mirror upstream handler failures, rather than the HTTP-error factory:
            // Guzzle 7 CurlFactory/on_headers use RequestException; Guzzle 8
            // sink/on_headers/framing failures use ResponseException subclasses.
            $responseClass = 'GuzzleHttp\\Exception\\ResponseException';
            $transferClass = 'GuzzleHttp\\Exception\\ResponseTransferException';
            if (class_exists($responseClass) && class_exists($transferClass)) {
                $failures = [
                    new $responseClass('Unable to write to stream', $this->request(), $response),
                    new $responseClass('An error was encountered during the on_headers event', $this->request(), $response, new RuntimeException('Header callback failed')),
                    new $responseClass('Framing failure', $this->request(), $response, new \OverflowException('Invalid content length')),
                    new $transferClass('Body copy failed', $this->request(), $response, new RuntimeException('Unable to read stream')),
                ];
            } else {
                // The base RequestException constructor changed in Guzzle 8.
                $requestClass = new \ReflectionClass(GuzzleRequestException::class);
                $failures = [
                    $requestClass->newInstance('cURL error 18: partial file', $this->request(), $response, null, ['errno' => 18]),
                    $requestClass->newInstance('cURL error 23: write error', $this->request(), $response, null, ['errno' => 23]),
                    $requestClass->newInstance('An error was encountered during the on_headers event', $this->request(), $response, new RuntimeException('Header callback failed')),
                    $requestClass->newInstance('Body copy failed', $this->request(), $response, new RuntimeException('Unable to write to stream')),
                ];
            }

            foreach ($failures as $failure) {
                foreach ([false, true] as $psr18) {
                    $this->assertPermanentFailure($failure, $psr18);
                }
            }
        }
    }

    public function testPublicOptionalResponseAccessorStillMapsCompletedHttpErrors(): void
    {
        foreach ([false, true] as $psr18) {
            $failure = new class($this->error(422, 'amount')) extends RuntimeException implements RequestExceptionInterface {
                private PsrResponse $response;

                public function __construct(PsrResponse $response)
                {
                    parent::__construct('Completed HTTP error');
                    $this->response = $response;
                }

                public function getRequest(): RequestInterface
                {
                    return new PsrRequest('GET', 'https://example.invalid');
                }

                public function getResponse(): PsrResponse
                {
                    return $this->response;
                }
            };
            $history = [];
            [$client, $handler] = $this->client([$failure, $this->payment()], $history, $psr18);

            try {
                $client->send(new GetPaymentRequest('tr_offline'));
                $this->fail('Expected validation failure.');
            } catch (ValidationException $e) {
                $this->assertSame(422, $e->getStatusCode());
                $this->assertSame('amount', $e->getField());
                $this->assertSame($failure, $e->getResponse()->getSenderException());
            }

            $this->assertSame(1, $handler->count());
            $this->assertCount(1, $history);
        }
    }

    public function testHttpStatusErrorsWithTransferFailureSignalsRemainPermanent(): void
    {
        foreach ([$this->error(422), $this->error(429)] as $response) {
            $failures = [GuzzleRequestException::create($this->request(), $response, new RuntimeException('Body copy failed'))];
            if (! class_exists('GuzzleHttp\\Exception\\ResponseException')) {
                $statusClass = new \ReflectionClass('GuzzleHttp\\Exception\\ClientException');
                $failures[] = $statusClass->newInstance('cURL error 18: partial file', $this->request(), $response, null, ['errno' => 18]);
            }

            foreach ($failures as $failure) {
                foreach ([false, true] as $psr18) {
                    $this->assertPermanentFailure($failure, $psr18);
                }
            }
        }
    }

    public function testCompletedGuzzleHttpErrorsKeepValidationAndRateLimitPolicies(): void
    {
        foreach ([$this->error(422, 'amount'), $this->error(429)] as $response) {
            foreach ([false, true] as $psr18) {
                // This is the same factory called by http_errors after fulfillment.
                $failure = GuzzleRequestException::create($this->request(), $response);
                $history = [];
                [$client, $handler] = $this->client([$failure, $this->payment()], $history, $psr18);

                try {
                    $client->send(new GetPaymentRequest('tr_offline'));
                    $this->fail('Expected completed HTTP error.');
                } catch (ApiException $e) {
                    $this->assertSame($response->getStatusCode(), $e->getStatusCode());
                    $this->assertSame($failure, $e->getResponse()->getSenderException());
                    if ($response->getStatusCode() === 422) {
                        $this->assertInstanceOf(ValidationException::class, $e);
                        $this->assertSame('amount', $e->getField());
                    } else {
                        $this->assertInstanceOf(TooManyRequestsException::class, $e);
                    }
                }

                $this->assertSame(1, $handler->count());
                $this->assertCount(1, $history);

                if ($response->getStatusCode() === 429) {
                    $history = [];
                    [$client, $handler] = $this->client([$failure, $this->payment()], $history, $psr18);
                    $client->setRetryStrategy(new ExponentialRetryStrategy(1, 0, 2.0, 0, false));
                    $this->assertInstanceOf(Payment::class, $client->send(new GetPaymentRequest('tr_offline')));
                    $this->assertSame(0, $handler->count());
                    $this->assertCount(2, $history);
                }
            }
        }
    }

    public function testInjectedHandlerAndConfigurationAreRetainedWhileFollowingRedirects(): void
    {
        $history = [];
        [$client, $handler, $guzzle] = $this->client([
            new PsrResponse(302, ['Location' => 'https://example.invalid/final']), $this->payment(),
        ], $history, false, [
            'timeout' => 19,
            'connect_timeout' => 5,
            'verify' => '/offline/custom-ca.pem',
            'proxy' => 'http://offline-proxy.invalid:8080',
            'headers' => ['X-Injected' => 'retained'],
            'allow_redirects' => ['max' => 2],
        ]);

        $this->assertInstanceOf(GuzzleMollieHttpAdapter::class, $client->getHttpClient());
        $property = new ReflectionProperty(GuzzleMollieHttpAdapter::class, 'httpClient');
        $property->setAccessible(true);
        $this->assertSame($guzzle, $property->getValue($client->getHttpClient()));
        $this->assertInstanceOf(Payment::class, $client->send(new GetPaymentRequest('tr_offline')));
        $this->assertSame(0, $handler->count());
        $this->assertCount(2, $history);
        foreach ($history as $attempt) {
            $this->assertSame(19, $attempt['options']['timeout']);
            $this->assertSame(5, $attempt['options']['connect_timeout']);
            $this->assertSame('/offline/custom-ca.pem', $attempt['options']['verify']);
            $this->assertSame('http://offline-proxy.invalid:8080', $attempt['options']['proxy']);
            $this->assertSame('retained', $attempt['request']->getHeaderLine('X-Injected'));
            $this->assertSame('Bearer access_offline', $attempt['request']->getHeaderLine('Authorization'));
            $this->assertStringContainsString('Guzzle/', $attempt['request']->getHeaderLine('User-Agent'));
        }
        $this->assertSame('https://example.invalid/final', (string) $history[1]['request']->getUri());
    }

    public function testDisabledRedirectsAndRedirectLimitsKeepTheirExistingFailureBehavior(): void
    {
        $history = [];
        [$client, $handler] = $this->client([
            new PsrResponse(302, ['Location' => 'https://example.invalid/final']), $this->payment(),
        ], $history, false, ['allow_redirects' => false]);

        try {
            $client->send(new GetPaymentRequest('tr_offline'));
            $this->fail('Expected unfollowed redirect.');
        } catch (ApiException $e) {
            $this->assertSame(302, $e->getStatusCode());
        }
        $this->assertCount(1, $history);
        $this->assertSame(1, $handler->count());

        $history = [];
        [$client, $handler] = $this->client([
            new PsrResponse(302, ['Location' => 'https://example.invalid/one']),
            new PsrResponse(302, ['Location' => 'https://example.invalid/two']), $this->payment(),
        ], $history, false, ['allow_redirects' => ['max' => 1]]);

        try {
            $client->send(new GetPaymentRequest('tr_offline'));
            $this->fail('Expected redirect-limit failure.');
        } catch (NetworkRequestException $e) {
            $this->assertNotInstanceOf(RetryableNetworkRequestException::class, $e);
            $this->assertInstanceOf(TooManyRedirectsException::class, $e->getPrevious());
        }
        $this->assertCount(2, $history);
        $this->assertSame(1, $handler->count());
    }

    public function testDefaultAdapterKeepsTlsTimeoutsAndHttpErrorsConfiguration(): void
    {
        $client = new MollieApiClient;
        $adapter = $client->getHttpClient();
        $this->assertInstanceOf(GuzzleMollieHttpAdapter::class, $adapter);
        $property = new ReflectionProperty(GuzzleMollieHttpAdapter::class, 'httpClient');
        $property->setAccessible(true);
        $guzzle = $property->getValue($adapter);

        $this->assertInstanceOf(Client::class, $guzzle);
        $this->assertSame(10, $guzzle->getConfig('timeout'));
        $this->assertSame(2, $guzzle->getConfig('connect_timeout'));
        $this->assertSame(CaBundle::getBundledCaBundlePath(), $guzzle->getConfig('verify'));
        $this->assertFalse($guzzle->getConfig('http_errors'));
        $this->assertInstanceOf(HandlerStack::class, $guzzle->getConfig('handler'));
    }

    private function assertPermanentFailure(RequestExceptionInterface $failure, bool $psr18): void
    {
        $history = [];
        [$client, $handler] = $this->client([$failure, $this->payment()], $history, $psr18);
        $client->setRetryStrategy(new ExponentialRetryStrategy(1, 0, 2.0, 0, false));
        $fatalCount = 0;
        $responseCount = 0;
        $client->middleware()->onResponse(function () use (&$responseCount) {
            $responseCount++;
        });
        $client->middleware()->onFatal(function () use (&$fatalCount) {
            $fatalCount++;
        });

        try {
            $client->send(new GetPaymentRequest('tr_offline'));
            $this->fail('A failed request must not become a payment or API response.');
        } catch (NetworkRequestException $e) {
            $this->assertNotInstanceOf(RetryableNetworkRequestException::class, $e);
            $this->assertSame($failure, $e->getPrevious());
        }

        $this->assertSame(1, $handler->count());
        $this->assertCount(1, $history);
        $this->assertSame(1, $fatalCount);
        $this->assertSame(0, $responseCount);
    }

    private function client(array $queue, &$history, bool $psr18 = false, array $config = []): array
    {
        $handler = new MockHandler($queue);
        $stack = HandlerStack::create($handler);
        $stack->push(Middleware::history($history));
        $guzzle = new Client($config + ['handler' => $stack]);
        if ($psr18) {
            $factory = new Psr17Factory;
            $adapter = new PSR18MollieHttpAdapter($guzzle, $factory, $factory, $factory, $factory);
            $client = new MollieApiClient($adapter);
        } else {
            $client = new MollieApiClient($guzzle);
        }
        $client->setAccessToken('access_offline');
        $client->setApiEndpoint('https://example.invalid');
        $client->setRetryStrategy(new LinearRetryStrategy(1, 0));

        return [$client, $handler, $guzzle];
    }

    private function networkExceptionClasses(): array
    {
        return array_filter([
            ConnectException::class,
            'GuzzleHttp\\Exception\\NetworkException',
            'GuzzleHttp\\Exception\\NetworkTimeoutException',
        ], 'class_exists');
    }

    private function request(): PsrRequest
    {
        return new PsrRequest('POST', 'https://example.invalid/v2/payments');
    }

    private function payment(): PsrResponse
    {
        return new PsrResponse(200, ['Content-Type' => 'application/hal+json'], '{"resource":"payment","id":"tr_offline"}');
    }

    private function error(int $status, ?string $field = null): PsrResponse
    {
        return new PsrResponse($status, ['Content-Type' => 'application/hal+json'], json_encode([
            'status' => $status,
            'title' => 'Offline error',
            'detail' => 'Offline failure',
            'field' => $field,
        ], JSON_THROW_ON_ERROR));
    }
}

<?php

declare(strict_types=1);

namespace Tests\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Exception\MalformedUriException;
use GuzzleHttp\Psr7\Response as PsrResponse;
use Mollie\Api\Exceptions\UntrustedUrlException;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\CursorCollection;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Resources\RefundCollection;
use Mollie\Api\Webhooks\WebhookEventMapper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class UntrustedUrlGuardTest extends TestCase
{
    #[Test]
    public function forged_link_in_unverified_webhook_payload_does_not_leak_credentials()
    {
        $client = new MockMollieClient([
            DynamicGetRequest::class => MockResponse::ok('refund-list'),
        ]);
        $client->setApiKey('live_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM');

        // Unsigned payload: the integrator skipped SignatureValidator.
        $event = (new WebhookEventMapper)->processPayload([
            'id' => 'event_forged',
            'type' => 'payment-link.paid',
            'entityId' => 'tr_forged',
            'createdAt' => '2024-12-16T15:57:04.0Z',
            '_embedded' => [
                'entity' => [
                    'resource' => 'payment',
                    'id' => 'tr_forged',
                    'mode' => 'live',
                    '_links' => [
                        'refunds' => ['href' => 'https://attacker.example/v2/payments/tr_forged/refunds'],
                    ],
                ],
            ],
            '_links' => [],
        ]);

        /** @var Payment $payment */
        $payment = $event->asResource($client);
        $this->assertInstanceOf(Payment::class, $payment);

        try {
            $payment->refunds();
            $this->fail('Expected UntrustedUrlException.');
        } catch (UntrustedUrlException $e) {
            $this->assertSame('https://attacker.example', $e->origin);
            $this->assertSame(['https://api.mollie.com'], $e->trustedOrigins);
            $this->assertStringNotContainsString('live_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM', $e->getMessage());
        }

        $client->assertSentCount(0);
    }

    #[Test]
    #[DataProvider('untrustedUrlProvider')]
    public function rejects_absolute_urls_outside_the_configured_origin_before_sending(string $url)
    {
        $client = new MockMollieClient([
            DynamicGetRequest::class => MockResponse::ok('payment-list'),
        ]);

        try {
            $client->send(new DynamicGetRequest($url));
            $this->fail('Expected UntrustedUrlException.');
        } catch (UntrustedUrlException $e) {
            $this->assertSame($url, $e->url);
        }

        $client->assertSentCount(0);
    }

    public static function untrustedUrlProvider(): array
    {
        return [
            'foreign host' => ['https://attacker.example/v2/payments'],
            'scheme downgrade' => ['http://api.mollie.com/v2/payments'],
            'non-default port' => ['https://api.mollie.com:8443/v2/payments'],
            'port zero' => ['https://api.mollie.com:0/v2/payments'],
            'ipv6 literal' => ['http://[::1]/v2/payments'],
            'userinfo confusion' => ['https://api.mollie.com@attacker.example/v2/payments'],
            'host suffix' => ['https://api.mollie.com.attacker.example/v2/payments'],
            'fragment confusion' => ['https://attacker.example#@api.mollie.com/'],
            'trailing dot host' => ['https://api.mollie.com./v2/payments'],
            'user info on trusted host' => ['https://attacker@api.mollie.com/v2/payments'],
            'password on trusted host' => ['https://attacker:secret@api.mollie.com/v2/payments'],
            'file scheme' => ['file:///etc/passwd'],
            'mailto scheme' => ['mailto:attacker@example.com'],
        ];
    }

    #[Test]
    public function rejects_forged_pagination_links()
    {
        $client = new MockMollieClient([
            DynamicGetRequest::class => MockResponse::ok('payment-list'),
        ]);

        $collection = new PaymentCollection($client, [], (object) [
            'next' => (object) ['href' => 'https://attacker.example/v2/payments?from=tr_x'],
        ]);

        $this->expectException(UntrustedUrlException::class);

        try {
            $collection->next();
        } finally {
            $client->assertSentCount(0);
        }
    }

    #[Test]
    public function no_http_request_reaches_the_transport_for_untrusted_urls()
    {
        $history = [];
        $handler = new MockHandler([new PsrResponse(200, [], '{}')]);
        $stack = HandlerStack::create($handler);
        $stack->push(Middleware::history($history));

        $client = new MollieApiClient(new Client(['handler' => $stack]));
        $client->setApiKey('live_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM');

        try {
            $client->send(new DynamicGetRequest('https://attacker.example/v2/payments'));
            $this->fail('Expected UntrustedUrlException.');
        } catch (UntrustedUrlException) {
        }

        $this->assertCount(0, $history);
        $this->assertSame(1, $handler->count());
    }

    #[Test]
    #[DataProvider('sameOriginUrlProvider')]
    public function same_origin_absolute_urls_are_still_sent(string $url)
    {
        $client = new MockMollieClient([
            DynamicGetRequest::class => MockResponse::ok('payment-list'),
        ]);

        $client->send(new DynamicGetRequest($url));

        $client->assertSent(fn (PendingRequest $request) => $request->url() === $url);
    }

    public static function sameOriginUrlProvider(): array
    {
        return [
            'pagination link' => ['https://api.mollie.com/v2/payments?from=tr_x&limit=5'],
            'explicit default port' => ['https://api.mollie.com:443/v2/payments'],
            'mixed case host' => ['https://API.Mollie.com/v2/payments'],
        ];
    }

    #[Test]
    public function same_origin_resource_links_are_still_followed()
    {
        $client = new MockMollieClient([
            DynamicGetRequest::class => MockResponse::ok('refund-list'),
        ]);

        $payment = new Payment($client);
        $payment->_links = (object) [
            'refunds' => (object) ['href' => 'https://api.mollie.com/v2/payments/tr_x/refunds'],
        ];

        $this->assertInstanceOf(RefundCollection::class, $payment->refunds());
        $client->assertSent(fn (PendingRequest $request) => $request->url() === 'https://api.mollie.com/v2/payments/tr_x/refunds');
    }

    #[Test]
    public function custom_api_endpoint_trusts_its_own_origin_and_mollies_canonical_origin_only()
    {
        $client = new MockMollieClient([
            DynamicGetRequest::class => MockResponse::ok('payment-list'),
        ], true);
        $client->setApiEndpoint('https://mollie-proxy.example');

        $client->send(new DynamicGetRequest('payments'));
        $client->send(new DynamicGetRequest('https://mollie-proxy.example/v2/payments?from=tr_x'));
        // Links returned through a transparent proxy keep pointing at Mollie.
        $client->send(new DynamicGetRequest('https://api.mollie.com/v2/payments?from=tr_x'));

        $client->assertSentCount(3);

        try {
            $client->send(new DynamicGetRequest('https://attacker.example/v2/payments'));
            $this->fail('Expected UntrustedUrlException.');
        } catch (UntrustedUrlException $e) {
            $this->assertSame(['https://mollie-proxy.example', 'https://api.mollie.com'], $e->trustedOrigins);
        }

        $client->assertSentCount(3);
    }

    #[Test]
    public function user_info_from_the_configured_endpoint_is_trusted()
    {
        $client = new MockMollieClient([
            DynamicGetRequest::class => MockResponse::ok('payment-list'),
        ], true);
        $client->setApiEndpoint('https://proxy-user:proxy-pass@mollie-proxy.example');

        $client->send(new DynamicGetRequest('payments'));
        $client->send(new DynamicGetRequest('https://proxy-user:proxy-pass@mollie-proxy.example/v2/payments?from=tr_x'));

        $client->assertSentCount(2);

        try {
            $client->send(new DynamicGetRequest('https://attacker@mollie-proxy.example/v2/payments'));
            $this->fail('Expected UntrustedUrlException.');
        } catch (UntrustedUrlException $e) {
            $this->assertSame('https://mollie-proxy.example', $e->origin);
            $this->assertStringNotContainsString('proxy-pass', $e->getMessage());
        }

        $client->assertSentCount(2);
    }

    #[Test]
    public function client_trusts_its_configured_endpoint_and_mollie()
    {
        $client = new MollieApiClient;

        $this->assertSame(['https://api.mollie.com/v2', 'https://api.mollie.com'], $client->trustedOrigins());

        $client->setApiEndpoint('https://mollie-proxy.example');

        $this->assertSame(['https://mollie-proxy.example/v2', 'https://api.mollie.com'], $client->trustedOrigins());
    }

    private const API_KEY = 'live_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM';

    public static function linkFollowingSinkProvider(): array
    {
        return [
            'Profile::payments' => ['profile', 'payments', 'https://api.mollie.com/v2/payments?profileId=pfl_v9hTwCvYqw'],
            'Profile::methods' => ['profile', 'methods', 'https://api.mollie.com/v2/methods?profileId=pfl_v9hTwCvYqw'],
            'Profile::refunds' => ['profile', 'refunds', 'https://api.mollie.com/v2/refunds?profileId=pfl_v9hTwCvYqw'],
            'Profile::chargebacks' => ['profile', 'chargebacks', 'https://api.mollie.com/v2/chargebacks?profileId=pfl_v9hTwCvYqw'],
            'Payment::refunds' => ['payment', 'refunds', 'https://api.mollie.com/v2/payments/tr_x/refunds'],
            'Payment::captures' => ['payment', 'captures', 'https://api.mollie.com/v2/payments/tr_x/captures'],
            'Payment::chargebacks' => ['payment', 'chargebacks', 'https://api.mollie.com/v2/payments/tr_x/chargebacks'],
            'Subscription::payments' => ['subscription', 'payments', 'https://api.mollie.com/v2/customers/cst_x/subscriptions/sub_x/payments'],
        ];
    }

    #[Test]
    #[DataProvider('linkFollowingSinkProvider')]
    public function every_link_following_sink_refuses_a_forged_webhook_link(string $resource, string $link, string $mollieHref)
    {
        [$client, $history, $handler] = $this->guzzleClient();

        $model = $this->forgedWebhookResource($client, $resource, $link, 'https://attacker.example/v2/'.$link);

        try {
            $model->{$link}();
            $this->fail('Expected UntrustedUrlException.');
        } catch (UntrustedUrlException $e) {
            $this->assertSame('https://attacker.example', $e->origin);
            $this->assertStringNotContainsString(self::API_KEY, (string) $e);
        }

        $this->assertCount(0, $history);
        $this->assertSame(1, $handler->count());
    }

    #[Test]
    #[DataProvider('linkFollowingSinkProvider')]
    public function every_link_following_sink_still_follows_real_mollie_links(string $resource, string $link, string $mollieHref)
    {
        [$client, $history] = $this->guzzleClient();

        $this->forgedWebhookResource($client, $resource, $link, $mollieHref)->{$link}();

        $this->assertCount(1, $history);
        $this->assertSame($mollieHref, (string) $history[0]['request']->getUri());
        $this->assertSame('Bearer '.self::API_KEY, $history[0]['request']->getHeaderLine('Authorization'));
    }

    public static function paginationSinkProvider(): array
    {
        return [
            'next()' => ['next', fn (CursorCollection $c) => $c->next()],
            'previous()' => ['previous', fn (CursorCollection $c) => $c->previous()],
            'getAutoIterator()' => ['next', fn (CursorCollection $c) => iterator_to_array($c->getAutoIterator(), false)],
            'getAutoIterator(true)' => ['previous', fn (CursorCollection $c) => iterator_to_array($c->getAutoIterator(true), false)],
        ];
    }

    #[Test]
    #[DataProvider('paginationSinkProvider')]
    public function every_pagination_sink_refuses_a_forged_link(string $rel, \Closure $follow)
    {
        [$client, $history] = $this->guzzleClient([
            '_embedded' => ['payments' => [['resource' => 'payment', 'id' => 'tr_1']]],
            '_links' => [$rel => ['href' => 'https://attacker.example/v2/payments?from=tr_2&limit=1']],
        ]);

        $firstPage = $client->payments->page();
        $this->assertCount(1, $history);

        $this->expectException(UntrustedUrlException::class);

        try {
            $follow($firstPage);
        } finally {
            $this->assertCount(1, $history); // the forged page was never requested
        }
    }

    #[Test]
    public function configured_user_info_is_required_on_links_to_the_configured_host()
    {
        $client = new MockMollieClient([
            DynamicGetRequest::class => MockResponse::ok('payment-list'),
        ], true);
        $client->setApiEndpoint('https://proxy-user:proxy-pass@mollie-proxy.example');

        try {
            $client->send(new DynamicGetRequest('https://mollie-proxy.example/v2/payments?from=tr_x'));
            $this->fail('Expected UntrustedUrlException.');
        } catch (UntrustedUrlException $e) {
            $this->assertStringNotContainsString('proxy-pass', $e->getMessage());
            $this->assertStringContainsString('does not match the trusted origin', $e->getMessage());
        }

        $client->assertSentCount(0);
    }

    #[Test]
    public function url_the_transport_cannot_parse_is_refused_as_untrusted()
    {
        [$client, $history] = $this->guzzleClient();

        try {
            // Valid per FILTER_VALIDATE_URL, so Url::join keeps it absolute, but Guzzle's URI factory rejects it.
            $client->send(new DynamicGetRequest('https://attacker.example:+443/v2/payments'));
            $this->fail('Expected UntrustedUrlException.');
        } catch (UntrustedUrlException $e) {
            $this->assertInstanceOf(MalformedUriException::class, $e->getPrevious());
        }

        $this->assertCount(0, $history);
    }

    /**
     * @return array{0: MollieApiClient, 1: mixed, 2: MockHandler}
     */
    private function guzzleClient(array $body = []): array
    {
        $history = new \ArrayObject;
        $handler = new MockHandler([
            new PsrResponse(200, ['Content-Type' => 'application/hal+json'], json_encode($body + [
                'count' => 0,
                '_embedded' => array_fill_keys(['payments', 'methods', 'refunds', 'chargebacks', 'captures'], []),
                '_links' => (object) [],
            ])),
        ]);
        $stack = HandlerStack::create($handler);
        $stack->push(Middleware::history($history));

        $client = new MollieApiClient(new Client(['handler' => $stack]));
        $client->setApiKey(self::API_KEY);

        return [$client, $history, $handler];
    }

    private function forgedWebhookResource(MollieApiClient $client, string $resource, string $link, string $href): object
    {
        // Unsigned payload: the integrator skipped SignatureValidator.
        return (new WebhookEventMapper)->processPayload([
            'id' => 'event_forged',
            'type' => 'payment-link.paid',
            'entityId' => 'x_forged',
            'createdAt' => '2024-12-16T15:57:04.0Z',
            '_embedded' => ['entity' => [
                'resource' => $resource,
                'id' => 'x_forged',
                'mode' => 'live',
                '_links' => [$link => ['href' => $href]],
            ]],
            '_links' => [],
        ])->asResource($client);
    }
}

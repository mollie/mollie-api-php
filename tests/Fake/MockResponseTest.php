<?php

declare(strict_types=1);

namespace Tests\Fake;

use Mollie\Api\Fake\ListResponseBuilder;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Fake\ResourceResponseBuilder;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class MockResponseTest extends TestCase
{
    #[Test]
    public function body_returns_json_string_directly()
    {
        $jsonString = "{\n  \"key\": \"value\\/ü\"\n}";
        $response = new MockResponse($jsonString);

        $this->assertSame($jsonString, $response->body());
    }

    #[Test]
    public function body_converts_array_to_json_string()
    {
        $array = ['key' => 'value'];
        $response = new MockResponse($array);

        $this->assertEquals(json_encode($array), $response->body());
    }

    #[Test]
    public function body_loads_json_file_when_not_json_string()
    {
        $response = new MockResponse('payment');

        $body = $response->body();

        $this->assertJson($body);
        $this->assertStringContainsString('payment', $body);
    }

    #[Test]
    public function body_replaces_resource_id_placeholder()
    {
        $resourceId = 'tr_"quoted"\\slashed/ü';
        $response = new MockResponse('payment', 200, $resourceId);

        $body = $response->json();

        $this->assertSame($resourceId, $body['id']);
    }

    #[Test]
    #[DataProvider('nestedFixtureProvider')]
    public function body_replaces_resource_id_in_nested_urls(string $fixture, array $paths): void
    {
        $resourceId = 'id_"quoted"\\slashed/ü';
        $body = (new MockResponse($fixture, 200, $resourceId))->json();

        foreach ($paths as $path) {
            $value = $body;

            foreach ($path as $key) {
                $value = $value[$key];
            }

            $this->assertStringContainsString($resourceId, $value);
        }
    }

    public static function nestedFixtureProvider(): array
    {
        return [
            'settlement' => [
                'settlement',
                [
                    ['_links', 'payments', 'href'],
                    ['_links', 'refunds', 'href'],
                    ['_links', 'chargebacks', 'href'],
                    ['_links', 'captures', 'href'],
                ],
            ],
            'cursor' => [
                'cursor-collection',
                [['_links', 'self', 'href'], ['_embedded', 'payments', 0, 'id']],
            ],
            'cursor next' => [
                'cursor-collection-next',
                [['_links', 'self', 'href'], ['_links', 'next', 'href']],
            ],
        ];
    }

    #[Test]
    public function body_replaces_resource_id_in_json_object_key(): void
    {
        $resourceId = 'payments_"quoted"\\slashed/ü';
        $body = (new MockResponse('empty-list', 200, $resourceId))->json();

        $this->assertArrayHasKey($resourceId, $body['_embedded']);
        $this->assertSame([], $body['_embedded'][$resourceId]);
    }

    #[Test]
    public function numeric_resource_id_remains_a_json_object_key(): void
    {
        $body = json_decode((new MockResponse('empty-list', 200, '0'))->body());

        $this->assertIsObject($body->_embedded);
        $this->assertObjectHasProperty('0', $body->_embedded);
    }

    #[Test]
    public function placeholder_shaped_resource_id_remains_literal_data(): void
    {
        $resourceId = 'tr_{{ RESOURCE_ID }}_literal';
        $body = (new MockResponse('payment', 200, $resourceId))->json();

        $this->assertSame($resourceId, $body['id']);
    }

    #[Test]
    public function list_returns_list_builder()
    {
        $response = MockResponse::list(PaymentCollection::class);

        $this->assertInstanceOf(ListResponseBuilder::class, $response);
    }

    #[Test]
    public function resource_returns_resource_builder()
    {
        $response = MockResponse::resource(Payment::class);

        $this->assertInstanceOf(ResourceResponseBuilder::class, $response);
    }

    #[Test]
    public function not_found_returns_404_status_code()
    {
        $response = MockResponse::notFound();

        $this->assertEquals(404, $response->createPsrResponse()->getStatusCode());
    }

    #[Test]
    public function unprocessable_entity_returns_422_status_code()
    {
        $response = MockResponse::unprocessableEntity();

        $this->assertEquals(422, $response->createPsrResponse()->getStatusCode());
    }

    #[Test]
    public function payment_round_trip_preserves_domain_and_transport_status(): void
    {
        $response = MockResponse::payment(id: 'tr_12345');

        $restored = unserialize(serialize($response));

        $this->assertInstanceOf(MockResponse::class, $restored);
        $this->assertSame('open', $restored->json()['status']);
        $this->assertSame(200, $restored->createPsrResponse()->getStatusCode());
    }

    #[Test]
    public function no_content_round_trip_preserves_backing_state(): void
    {
        $response = MockResponse::noContent('webhooks');

        $restored = unserialize(serialize($response));

        $this->assertInstanceOf(MockResponse::class, $restored);
        $this->assertSame('', $restored->body());
        $this->assertSame(204, $restored->createPsrResponse()->getStatusCode());
        $this->assertSame([
            'version' => 2,
            'body' => '',
            'status' => 204,
            'resourceKey' => 'webhooks',
        ], $restored->__serialize());
    }

    #[Test]
    public function named_fixture_round_trip_preserves_fixture_and_resource_key(): void
    {
        $response = new MockResponse('payment', 200, 'tr_12345');

        $restored = unserialize(serialize($response));

        $this->assertInstanceOf(MockResponse::class, $restored);
        $this->assertSame('payment', $restored->__serialize()['body']);
        $this->assertSame('tr_12345', $restored->__serialize()['resourceKey']);
        $this->assertJsonStringEqualsJsonString($response->body(), $restored->body());
    }

    #[Test]
    public function custom_json_round_trip_preserves_body_and_transport_status(): void
    {
        $body = "{\n  \"status\": \"open\",\n  \"custom\": true\n}";
        $response = new MockResponse($body, 418);

        $restored = unserialize(serialize($response));

        $this->assertInstanceOf(MockResponse::class, $restored);
        $this->assertSame([
            'version' => 2,
            'body' => $body,
            'status' => 418,
            'resourceKey' => '',
        ], $restored->__serialize());
        $this->assertSame($body, $restored->body());
        $this->assertSame(418, $restored->createPsrResponse()->getStatusCode());
    }

    #[Test]
    public function valid_unversioned_legacy_data_is_accepted(): void
    {
        $response = new MockResponse([]);

        $response->__unserialize([
            'body' => 'payment',
            'status' => 202,
            'resourceKey' => 'tr_legacy',
        ]);

        $this->assertSame(202, $response->createPsrResponse()->getStatusCode());
        $this->assertStringContainsString('tr_legacy', $response->body());
    }

    #[Test]
    #[DataProvider('invalidSerializedDataProvider')]
    public function invalid_serialized_data_is_rejected(array $data, string $message): void
    {
        $response = new MockResponse([]);

        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage($message);

        $response->__unserialize($data);
    }

    public static function invalidSerializedDataProvider(): array
    {
        return [
            'legacy domain status' => [
                ['body' => '{"status":"open"}', 'status' => 'open', 'resourceKey' => ''],
                'Invalid MockResponse serialized data types.',
            ],
            'missing key' => [
                ['body' => '{}', 'status' => 200],
                'Invalid MockResponse serialized data shape.',
            ],
            'wrong versioned type' => [
                ['version' => 2, 'body' => [], 'status' => 200, 'resourceKey' => ''],
                'Invalid MockResponse serialized data types.',
            ],
            'unknown version' => [
                ['version' => 3, 'body' => '{}', 'status' => 200, 'resourceKey' => ''],
                'Unsupported MockResponse serialization version.',
            ],
        ];
    }
}

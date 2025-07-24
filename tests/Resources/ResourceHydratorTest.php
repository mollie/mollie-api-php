<?php

namespace Tests\Resources;

use Mollie\Api\Contracts\EmbeddedResourcesContract;
use Mollie\Api\Exceptions\EmbeddedResourcesNotParseableException;
use Mollie\Api\Http\Response;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\AnyResource;
use Mollie\Api\Resources\BaseResource;
use Mollie\Api\Resources\Client;
use Mollie\Api\Resources\Onboarding;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Resources\RefundCollection;
use Mollie\Api\Resources\ResourceHydrator;
use PHPUnit\Framework\TestCase;

class ResourceHydratorTest extends TestCase
{
    private ResourceHydrator $hydrator;

    private MollieApiClient $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->hydrator = new ResourceHydrator;
        $this->client = $this->createMock(MollieApiClient::class);
    }

    /** @test */
    public function it_hydrates_from_api_result()
    {
        $apiResult = [
            'resource' => 'payment',
            'id' => 'tr_44aKxzEbr8',
            'mode' => 'test',
            'createdAt' => '2018-03-13T14:02:29+00:00',
            'amount' => [
                'value' => '20.00',
                'currency' => 'EUR',
            ],
        ];

        $resource = new Payment($this->client);
        $response = $this->createMock(Response::class);

        $this->hydrator->hydrate($resource, $apiResult, $response);

        $this->assertEquals('payment', $resource->resource);
        $this->assertEquals('tr_44aKxzEbr8', $resource->id);
        $this->assertEquals('test', $resource->mode);
        $this->assertEquals('2018-03-13T14:02:29+00:00', $resource->createdAt);
        $this->assertEquals(['value' => '20.00', 'currency' => 'EUR'], $resource->amount);
    }

    /** @test */
    public function it_hydrates_embedded_collections()
    {
        $apiResult = [
            'resource' => 'payment',
            'id' => 'tr_44aKxzEbr8',
            '_embedded' => (object) [
                'refunds' => [
                    [
                        'resource' => 'refund',
                        'id' => 're_4qqhO89gsT',
                        'amount' => [
                            'value' => '20.00',
                            'currency' => 'EUR',
                        ],
                    ],
                ],
            ],
        ];

        $resource = new Payment($this->client);
        $response = $this->createMock(Response::class);

        $this->hydrator->hydrate($resource, $apiResult, $response);

        $this->assertInstanceOf(RefundCollection::class, $resource->refunds());
    }

    /** @test */
    public function it_hydrates_embedded_resources()
    {
        $apiResult = [
            'resource' => 'client',
            'id' => 'org_1337',
            '_embedded' => (object) [
                'onboarding' => (object) [
                    'resource' => 'onboarding',
                    'name' => 'Mollie B.V.',
                    'status' => 'completed',
                ],
            ],
        ];

        $resource = new Client($this->client);
        $response = $this->createMock(Response::class);

        $this->hydrator->hydrate($resource, $apiResult, $response);

        $this->assertInstanceOf(Onboarding::class, $resource->_embedded->onboarding);
    }

    /** @test */
    public function it_hydrates_a_collection()
    {
        $collection = new PaymentCollection($this->client);
        $items = [
            ['id' => 'payment-1', 'resource' => 'payment'],
            ['id' => 'payment-2', 'resource' => 'payment'],
        ];
        $response = $this->createMock(Response::class);
        $response->method('getConnector')->willReturn($this->client);

        $result = $this->hydrator->hydrateCollection($collection, $items, $response);

        $this->assertCount(2, $result);
        $this->assertInstanceOf(Payment::class, $result[0]);
        $this->assertEquals('payment-1', $result[0]->id);
    }

    /** @test */
    public function it_hydrates_a_simple_resource()
    {
        $data = ['id' => 'test_123', 'name' => 'Test Resource'];
        $response = $this->createMock(Response::class);

        /** @var AnyResource $resource */
        $resource = $this->hydrator->hydrate(new AnyResource($this->client), $data, $response);

        $this->assertEquals('test_123', $resource->id);
        $this->assertEquals('Test Resource', $resource->name);
    }

    /** @test */
    public function it_throws_exception_for_unmapped_embedded_resources()
    {
        $resource = new class($this->client) extends BaseResource implements EmbeddedResourcesContract {
            public function getEmbeddedResourcesMap(): array
            {
                return [];
            }
        };

        $data = [
            '_embedded' => (object) [
                'unknown' => ['id' => 'test'],
            ],
        ];

        $response = $this->createMock(Response::class);

        $this->expectException(EmbeddedResourcesNotParseableException::class);
        $this->hydrator->hydrate($resource, $data, $response);
    }
}

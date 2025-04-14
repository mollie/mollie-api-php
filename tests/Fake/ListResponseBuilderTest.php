<?php

namespace Tests\Fake;

use Mollie\Api\Exceptions\LogicException;
use Mollie\Api\Fake\ListResponseBuilder as BaseListResponseBuilder;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Utils\Arr;
use PHPUnit\Framework\TestCase;

class ListResponseBuilderTest extends TestCase
{
    /** @test */
    public function throws_exception_when_collection_class_is_not_a_subclass_of_resource_collection()
    {
        $this->expectException(LogicException::class);

        new ListResponseBuilder(Payment::class);
    }

    /** @test */
    public function it_can_add_items()
    {
        $builder = new ListResponseBuilder(PaymentCollection::class);

        $builder->add([
            'id' => 'tr_12345',
            'status' => 'pending',
        ]);

        $this->assertEquals([
            'id' => 'tr_12345',
            'status' => 'pending',
        ], $builder->items()[0]);
    }

    /** @test */
    public function it_can_add_many_items()
    {
        $builder = new ListResponseBuilder(PaymentCollection::class);

        $builder->addMany([
            [
                'id' => 'tr_12345',
                'status' => 'pending',
            ],
            [
                'id' => 'tr_12346',
                'status' => 'pending',
            ],
        ]);

        $this->assertEquals([
            [
                'id' => 'tr_12345',
                'status' => 'pending',
            ],
            [
                'id' => 'tr_12346',
                'status' => 'pending',
            ],
        ], $builder->items());
    }

    /** @test */
    public function can_create_a_mock_response()
    {
        $builder = new ListResponseBuilder(PaymentCollection::class);

        $builder->add([
            'id' => 'tr_12345',
            'status' => 'pending',
        ]);

        $response = $builder->create();

        $this->assertInstanceOf(MockResponse::class, $response);
        $this->assertEquals([
            'count' => 1,
            '_embedded' => [
                'payments' => [
                    [
                        'id' => 'tr_12345',
                        'status' => 'pending',
                    ],
                ],
            ],
        ], Arr::except($response->json(), ['_links']));
    }
}

class ListResponseBuilder extends BaseListResponseBuilder
{
    public function items(): array
    {
        return $this->items;
    }
}

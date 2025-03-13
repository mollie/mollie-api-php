<?php

namespace Tests\Fake;

use Mollie\Api\Exceptions\LogicException;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Fake\ResourceResponseBuilder;
use Mollie\Api\Resources\ChargebackCollection;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\RefundCollection;
use Mollie\Api\Types\PaymentStatus;
use Mollie\Api\Types\RefundStatus;
use Mollie\Api\Utils\Arr;
use PHPUnit\Framework\TestCase;
use stdClass;

class ResourceResponseBuilderTest extends TestCase
{
    /** @test */
    public function it_can_create_a_resource_response()
    {
        $builder = new ResourceResponseBuilder(Payment::class);

        /** @var MockResponse $response */
        $response = $builder
            ->with([
                'resource' => 'payment',
                'id' => 'tr_foobarfoobar',
                'status' => PaymentStatus::PAID,
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '14.52',
                ],
            ])
            ->create();

        $this->assertInstanceOf(MockResponse::class, $response);
        $this->assertEquals([
            'resource' => 'payment',
            'id' => 'tr_foobarfoobar',
            'status' => PaymentStatus::PAID,
            'amount' => [
                'currency' => 'EUR',
                'value' => '14.52',
            ],
        ], Arr::except($response->json(), ['_links']));
    }

    /** @test */
    public function it_can_create_a_resource_response_with_embedded_resources()
    {
        $builder = new ResourceResponseBuilder(Payment::class);

        /** @var MockResponse $response */
        $response = $builder
            ->with([
                'resource' => 'payment',
                'id' => 'tr_foobarfoobar',
                'status' => PaymentStatus::PAID,
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '14.52',
                ],
                'createdAt' => '2023-11-07T14:42:51+00:00',
                'description' => 'the description',
                'paymentId' => 'tr_foobarfoobar',
            ])
            ->embed(RefundCollection::class)
            ->add([
                'resource' => 'refund',
                'id' => 're_cFiJjuLhSw',
                'status' => RefundStatus::REFUNDED,
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '14.52',
                ],
                'paymentId' => 'tr_foobarfoobar',
            ])
            ->create();

        $this->assertInstanceOf(MockResponse::class, $response);
        $this->assertEquals([
            'resource' => 'payment',
            'id' => 'tr_foobarfoobar',
            'status' => PaymentStatus::PAID,
            'amount' => [
                'currency' => 'EUR',
                'value' => '14.52',
            ],
            'createdAt' => '2023-11-07T14:42:51+00:00',
            'description' => 'the description',
            'paymentId' => 'tr_foobarfoobar',
            '_embedded' => [
                'refunds' => [
                    [
                        'resource' => 'refund',
                        'id' => 're_cFiJjuLhSw',
                        'status' => RefundStatus::REFUNDED,
                        'amount' => [
                            'currency' => 'EUR',
                            'value' => '14.52',
                        ],
                        'paymentId' => 'tr_foobarfoobar',
                    ],
                ],
            ],
        ], Arr::except($response->json(), ['_links']));
    }

    /** @test */
    public function it_can_add_multiple_embedded_resources()
    {
        $builder = new ResourceResponseBuilder(Payment::class);

        /** @var MockResponse $response */
        $response = $builder
            ->with([
                'resource' => 'payment',
                'id' => 'tr_foobarfoobar',
            ])
            ->embed(RefundCollection::class)
            ->addMany([
                [
                    'resource' => 'refund',
                    'id' => 're_1',
                    'status' => RefundStatus::REFUNDED,
                ],
                [
                    'resource' => 'refund',
                    'id' => 're_2',
                    'status' => RefundStatus::PENDING,
                ],
            ])
            ->create();

        $embedded = $response->json()['_embedded']['refunds'];
        $this->assertCount(2, $embedded);
        $this->assertEquals('re_1', $embedded[0]['id']);
        $this->assertEquals('re_2', $embedded[1]['id']);
    }

    /** @test */
    public function it_can_embed_multiple_collections()
    {
        $builder = new ResourceResponseBuilder(Payment::class);

        /** @var MockResponse $response */
        $response = $builder
            ->with([
                'resource' => 'payment',
                'id' => 'tr_foobarfoobar',
            ])
            ->embed(RefundCollection::class)
            ->add([
                'resource' => 'refund',
                'id' => 're_1',
                'status' => RefundStatus::REFUNDED,
            ])
            ->embed(ChargebackCollection::class)
            ->add([
                'resource' => 'chargeback',
                'id' => 'chb_1',
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '14.52',
                ],
            ])
            ->create();

        $embedded = $response->json()['_embedded'];

        // Assert refunds are present
        $this->assertArrayHasKey('refunds', $embedded);
        $this->assertCount(1, $embedded['refunds']);
        $this->assertEquals('re_1', $embedded['refunds'][0]['id']);

        // Assert chargebacks are present
        $this->assertArrayHasKey('chargebacks', $embedded);
        $this->assertCount(1, $embedded['chargebacks']);
        $this->assertEquals('chb_1', $embedded['chargebacks'][0]['id']);
    }

    /** @test */
    public function it_can_switch_between_embedded_collections()
    {
        $builder = new ResourceResponseBuilder(Payment::class);

        /** @var MockResponse $response */
        $response = $builder
            ->with([
                'resource' => 'payment',
                'id' => 'tr_foobarfoobar',
            ])
            ->embed(RefundCollection::class)
            ->add([
                'resource' => 'refund',
                'id' => 're_1',
            ])
            ->embed(ChargebackCollection::class)
            ->add([
                'resource' => 'chargeback',
                'id' => 'chb_1',
            ])
            ->embed(RefundCollection::class) // Switch back to refunds
            ->add([
                'resource' => 'refund',
                'id' => 're_2',
            ])
            ->create();

        $embedded = $response->json()['_embedded'];

        // Assert both refunds were added
        $this->assertCount(2, $embedded['refunds']);
        $this->assertEquals('re_1', $embedded['refunds'][0]['id']);
        $this->assertEquals('re_2', $embedded['refunds'][1]['id']);

        // Assert chargeback was added
        $this->assertCount(1, $embedded['chargebacks']);
        $this->assertEquals('chb_1', $embedded['chargebacks'][0]['id']);
    }

    /** @test */
    public function it_omits_embedded_key_when_no_collections_are_embedded()
    {
        $builder = new ResourceResponseBuilder(Payment::class);

        /** @var MockResponse $response */
        $response = $builder
            ->with([
                'resource' => 'payment',
                'id' => 'tr_foobarfoobar',
            ])
            ->create();

        $this->assertArrayNotHasKey('_embedded', $response->json());
    }

    /** @test */
    public function it_throws_an_exception_when_resource_class_is_invalid()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Resource class must be a subclass of Mollie\Api\Resources\BaseResource');

        new ResourceResponseBuilder(stdClass::class);
    }

    /** @test */
    public function it_throws_an_exception_when_calling_undefined_methods()
    {
        $builder = new ResourceResponseBuilder(Payment::class);

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Method undefinedMethod does not exist');

        /** @phpstan-ignore-next-line */
        $builder->undefinedMethod();
    }

    /** @test */
    public function it_throws_an_exception_when_adding_items_without_embedding_first()
    {
        $builder = new ResourceResponseBuilder(Payment::class);

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Method add does not exist');

        $builder->add(['id' => 'test']);
    }
}

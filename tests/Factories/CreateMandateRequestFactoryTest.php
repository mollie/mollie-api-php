<?php

namespace Tests\Factories;

use Mollie\Api\Factories\CreateMandateRequestFactory;
use Mollie\Api\Http\Requests\CreateMandateRequest;
use PHPUnit\Framework\TestCase;

class CreateMandateRequestFactoryTest extends TestCase
{
    private const CUSTOMER_ID = 'cst_12345';

    /** @test */
    public function create_returns_mandate_request_object_with_full_data()
    {
        $request = CreateMandateRequestFactory::new(self::CUSTOMER_ID)
            ->withPayload([
                'method' => 'directdebit',
                'consumerName' => 'John Doe',
                'consumerAccount' => 'NL55INGB0000000000',
                'consumerBic' => 'INGBNL2A',
                'consumerEmail' => 'john@example.com',
                'signatureDate' => '2024-01-01',
                'mandateReference' => 'MANDATE-12345',
                'paypalBillingAgreementId' => null,
            ])
            ->create();

        $this->assertInstanceOf(CreateMandateRequest::class, $request);
    }

    /** @test */
    public function create_returns_mandate_request_object_with_minimal_data()
    {
        $request = CreateMandateRequestFactory::new(self::CUSTOMER_ID)
            ->withPayload([
                'method' => 'directdebit',
                'consumerName' => 'John Doe',
            ])
            ->create();

        $this->assertInstanceOf(CreateMandateRequest::class, $request);
    }

    /** @test */
    public function create_returns_mandate_request_object_with_partial_data()
    {
        $request = CreateMandateRequestFactory::new(self::CUSTOMER_ID)
            ->withPayload([
                'method' => 'directdebit',
                'consumerName' => 'John Doe',
                'consumerAccount' => 'NL55INGB0000000000',
                'consumerBic' => 'INGBNL2A',
            ])
            ->create();

        $this->assertInstanceOf(CreateMandateRequest::class, $request);
    }

    /** @test */
    public function create_throws_exception_when_required_fields_are_missing()
    {
        $this->expectException(\Mollie\Api\Exceptions\LogicException::class);
        $this->expectExceptionMessage('Method and consumerName are required for creating a mandate');

        CreateMandateRequestFactory::new(self::CUSTOMER_ID)
            ->create();
    }

    /** @test */
    public function create_throws_exception_when_method_is_missing()
    {
        $this->expectException(\Mollie\Api\Exceptions\LogicException::class);
        $this->expectExceptionMessage('Method and consumerName are required for creating a mandate');

        CreateMandateRequestFactory::new(self::CUSTOMER_ID)
            ->withPayload([
                'consumerName' => 'John Doe',
            ])
            ->create();
    }

    /** @test */
    public function create_throws_exception_when_consumer_name_is_missing()
    {
        $this->expectException(\Mollie\Api\Exceptions\LogicException::class);
        $this->expectExceptionMessage('Method and consumerName are required for creating a mandate');

        CreateMandateRequestFactory::new(self::CUSTOMER_ID)
            ->withPayload([
                'method' => 'directdebit',
            ])
            ->create();
    }
}

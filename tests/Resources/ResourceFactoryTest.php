<?php

namespace Tests\Resources;

use Mollie\Api\Contracts\IsWrapper;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Onboarding;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Resources\ResourceFactory;
use PHPUnit\Framework\TestCase;

class ResourceFactoryTest extends TestCase
{
    /** @test */
    public function it_creates_a_resource_instance()
    {
        $connector = $this->createMock(MollieApiClient::class);
        $resource = ResourceFactory::create($connector, Payment::class);

        $this->assertInstanceOf(Payment::class, $resource);
    }

    /** @test */
    public function it_creates_a_collection_instance()
    {
        $connector = $this->createMock(MollieApiClient::class);
        $collection = ResourceFactory::createCollection(
            $connector,
            PaymentCollection::class
        );

        $this->assertInstanceOf(PaymentCollection::class, $collection);
    }

    /** @test */
    public function it_creates_a_decorated_resource()
    {
        $connector = $this->createMock(MollieApiClient::class);
        /** @var Onboarding $resource */
        $resource = ResourceFactory::create($connector, Onboarding::class);

        $resource->status = 'completed';
        $resource->canReceivePayments = true;
        $resource->canReceiveSettlements = true;
        $resource->_links = (object) [
            'dashboard' => (object) [
                'href' => 'https://dashboard.mollie.com',
            ],
        ];

        $decoratedResource = ResourceFactory::createDecoratedResource(
            $resource,
            CustomResourceDecorator::class
        );

        $this->assertInstanceOf(CustomResourceDecorator::class, $decoratedResource);
    }

    /** @test */
    public function it_throws_exception_for_invalid_decorator()
    {
        $connector = $this->createMock(MollieApiClient::class);
        $resource = ResourceFactory::create($connector, Onboarding::class);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("The decorator class 'InvalidDecorator' does not implement the ResourceDecorator interface.");

        ResourceFactory::createDecoratedResource($resource, 'InvalidDecorator');
    }
}

class CustomResourceDecorator implements IsWrapper
{
    public string $status;

    public bool $canReceivePayments;

    public bool $canReceiveSettlements;

    public string $dashboardUrl;

    public function __construct(
        string $status,
        bool $canReceivePayments,
        bool $canReceiveSettlements,
        string $dashboardUrl
    ) {
        $this->status = $status;
        $this->canReceivePayments = $canReceivePayments;
        $this->canReceiveSettlements = $canReceiveSettlements;
        $this->dashboardUrl = $dashboardUrl;
    }

    public static function fromResource($onboarding): IsWrapper
    {
        /** @var Onboarding $onboarding */
        return new self(
            $onboarding->status,
            $onboarding->canReceivePayments,
            $onboarding->canReceiveSettlements,
            $onboarding->_links->dashboard->href
        );
    }
}

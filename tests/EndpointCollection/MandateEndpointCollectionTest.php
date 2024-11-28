<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Http\Requests\CreateMandateRequest;
use Mollie\Api\Http\Requests\GetMandateRequest;
use Mollie\Api\Http\Requests\GetPaginatedMandateRequest;
use Mollie\Api\Http\Requests\RevokeMandateRequest;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\Mandate;
use Mollie\Api\Resources\MandateCollection;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class MandateEndpointCollectionTest extends TestCase
{
    /** @test */
    public function create_for_test()
    {
        $client = new MockClient([
            CreateMandateRequest::class => new MockResponse(201, 'mandate'),
        ]);

        $customer = new Customer($client);
        $customer->id = 'cst_4qqhO89gsT';

        /** @var Mandate $mandate */
        $mandate = $client->mandates->createFor($customer, [
            'method' => 'directdebit',
            'consumerName' => 'John Doe',
            'consumerAccount' => 'NL55INGB0000000000',
            'consumerBic' => 'INGBNL2A',
            'signatureDate' => '2023-05-07',
            'mandateReference' => 'EXAMPLE-CORP-MD13804',
        ]);

        $this->assertMandate($mandate);
    }

    /** @test */
    public function get_for_test()
    {
        $client = new MockClient([
            GetMandateRequest::class => new MockResponse(200, 'mandate'),
        ]);

        $customer = new Customer($client);
        $customer->id = 'cst_4qqhO89gsT';

        /** @var Mandate $mandate */
        $mandate = $client->mandates->getFor($customer, 'mdt_h3gAaD5zP');

        $this->assertMandate($mandate);
    }

    /** @test */
    public function revoke_for_test()
    {
        $client = new MockClient([
            RevokeMandateRequest::class => new MockResponse(204),
        ]);

        $customer = new Customer($client);
        $customer->id = 'cst_4qqhO89gsT';

        $client->mandates->revokeFor($customer, 'mdt_h3gAaD5zP');

        // Test passes if no exception is thrown
        $this->assertTrue(true);
    }

    /** @test */
    public function page_for_test()
    {
        $client = new MockClient([
            GetPaginatedMandateRequest::class => new MockResponse(200, 'mandate-list'),
        ]);

        $customer = new Customer($client);
        $customer->id = 'cst_4qqhO89gsT';

        /** @var MandateCollection $mandates */
        $mandates = $client->mandates->pageFor($customer);

        $this->assertInstanceOf(MandateCollection::class, $mandates);
        $this->assertEquals(1, $mandates->count());
        $this->assertCount(1, $mandates);

        $this->assertMandate($mandates[0]);
    }

    protected function assertMandate(Mandate $mandate)
    {
        $this->assertInstanceOf(Mandate::class, $mandate);
        $this->assertEquals('mandate', $mandate->resource);
        $this->assertEquals('live', $mandate->mode);
        $this->assertEquals('valid', $mandate->status);
        $this->assertEquals('directdebit', $mandate->method);
        $this->assertEquals('EXAMPLE-CORP-MD13804', $mandate->mandateReference);
        $this->assertEquals('2023-05-07', $mandate->signatureDate);
        $this->assertEquals('cst_4qqhO89gsT', $mandate->customerId);
        $this->assertNotEmpty($mandate->createdAt);
    }
}

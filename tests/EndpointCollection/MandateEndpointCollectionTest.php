<?php

namespace Tests\EndpointCollection;

use DateTimeImmutable;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\CreateMandateRequest;
use Mollie\Api\Http\Requests\GetMandateRequest;
use Mollie\Api\Http\Requests\GetPaginatedMandateRequest;
use Mollie\Api\Http\Requests\RevokeMandateRequest;
use Mollie\Api\Resources\Customer;
use Mollie\Api\Resources\Mandate;
use Mollie\Api\Resources\MandateCollection;
use PHPUnit\Framework\TestCase;

class MandateEndpointCollectionTest extends TestCase
{
    /** @test */
    public function create_for()
    {
        $client = new MockMollieClient([
            CreateMandateRequest::class => MockResponse::created('mandate'),
        ]);

        $customer = new Customer($client);
        $customer->id = 'cst_4qqhO89gsT';

        /** @var Mandate $mandate */
        $mandate = $client->mandates->createFor($customer, [
            'method' => 'directdebit',
            'consumerName' => 'John Doe',
            'iban' => 'NL55INGB0000000000',
            'bic' => 'INGBNL2A',
            'email' => 'john.doe@example.com',
            'signatureDate' => new DateTimeImmutable('2023-05-07'),
            'mandateReference' => 'EXAMPLE-CORP-MD13804',
        ]);

        $this->assertMandate($mandate);
    }

    /** @test */
    public function get_for()
    {
        $client = new MockMollieClient([
            GetMandateRequest::class => MockResponse::ok('mandate'),
        ]);

        $customer = new Customer($client);
        $customer->id = 'cst_4qqhO89gsT';

        /** @var Mandate $mandate */
        $mandate = $client->mandates->getFor($customer, 'mdt_h3gAaD5zP');

        $this->assertMandate($mandate);
    }

    /** @test */
    public function revoke_for()
    {
        $client = new MockMollieClient([
            RevokeMandateRequest::class => MockResponse::noContent(),
        ]);

        $customer = new Customer($client);
        $customer->id = 'cst_4qqhO89gsT';

        $client->mandates->revokeFor($customer, 'mdt_h3gAaD5zP');

        // Test passes if no exception is thrown
        $this->assertTrue(true);
    }

    /** @test */
    public function page_for()
    {
        $client = new MockMollieClient([
            GetPaginatedMandateRequest::class => MockResponse::ok('mandate-list'),
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

<?php

declare(strict_types=1);

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Fake\SequenceMockResponse;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedMandateRequest;
use Mollie\Api\Resources\Mandate;
use Mollie\Api\Resources\MandateCollection;
use Mollie\Api\Types\MandateQuery;
use PHPUnit\Framework\TestCase;

class GetPaginatedMandateRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_mandates()
    {
        $client = new MockMollieClient([
            GetPaginatedMandateRequest::class => MockResponse::ok('mandate-list'),
        ]);

        $request = new GetPaginatedMandateRequest('cst_kEn1PlbGa');

        /** @var MandateCollection */
        $mandates = $client->send($request);

        $this->assertTrue($mandates->getResponse()->successful());
        $this->assertInstanceOf(MandateCollection::class, $mandates);
        $this->assertGreaterThan(0, $mandates->count());

        foreach ($mandates as $mandate) {
            $this->assertInstanceOf(Mandate::class, $mandate);
            $this->assertEquals('mandate', $mandate->resource);
        }
    }

    /** @test */
    public function it_can_iterate_over_mandates()
    {
        $client = new MockMollieClient([
            GetPaginatedMandateRequest::class => MockResponse::ok('mandate-list'),
            DynamicGetRequest::class => new SequenceMockResponse(
                MockResponse::ok('mandate-list'),
                MockResponse::ok('empty-list', 'mandates'),
            ),
        ]);

        $request = (new GetPaginatedMandateRequest('cst_kEn1PlbGa'))->useIterator();

        /** @var MandateCollection */
        $mandates = $client->send($request);
        $this->assertTrue($mandates->getResponse()->successful());

        foreach ($mandates as $mandate) {
            $this->assertInstanceOf(Mandate::class, $mandate);
        }

        $client->assertSentCount(3);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $customerId = 'cst_kEn1PlbGa';
        $request = new GetPaginatedMandateRequest($customerId);

        $this->assertEquals("customers/{$customerId}/mandates", $request->resolveResourcePath());
    }

    /** @test */
    public function it_does_not_include_scopes_in_query_by_default()
    {
        $client = new MockMollieClient([
            GetPaginatedMandateRequest::class => MockResponse::ok('mandate-list'),
        ]);

        $client->send(new GetPaginatedMandateRequest('cst_kEn1PlbGa'));

        $client->assertSent(function (PendingRequest $pendingRequest) {
            $this->assertStringNotContainsString('scopes', $pendingRequest->getUri()->getQuery());

            return true;
        });
    }

    /** @test */
    public function it_sends_scopes_as_array_in_query()
    {
        $client = new MockMollieClient([
            GetPaginatedMandateRequest::class => MockResponse::ok('mandate-list'),
        ]);

        $request = new GetPaginatedMandateRequest(
            'cst_kEn1PlbGa',
            null,
            null,
            [
                MandateQuery::SCOPE_CUSTOMER_PRESENT,
                MandateQuery::SCOPE_CUSTOMER_NOT_PRESENT,
            ]
        );

        $client->send($request);

        $client->assertSent(function (PendingRequest $pendingRequest) {
            $query = $pendingRequest->getUri()->getQuery();

            // http_build_query renders array params as scopes[0]=customer-present
            // which Mollie accepts as the standard scopes[]=customer-present form.
            $this->assertStringContainsString('scopes%5B0%5D=customer-present', $query);
            $this->assertStringContainsString('scopes%5B1%5D=customer-not-present', $query);

            return true;
        });
    }
}

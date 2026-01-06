<?php

namespace Tests\Factories;

use Mollie\Api\Factories\GetPaginatedClientRequestFactory;
use Mollie\Api\Http\Requests\GetPaginatedClientRequest;
use Mollie\Api\Types\ClientQuery;
use PHPUnit\Framework\TestCase;

class GetPaginatedClientRequestFactoryTest extends TestCase
{
    /** @test */
    public function create_returns_paginated_client_request_object_with_full_data()
    {
        $request = GetPaginatedClientRequestFactory::new()
            ->withQuery([
                'from' => 'org_12345',
                'limit' => 50,
                'embed' => ['organization', 'onboarding'],
            ])
            ->create();

        $this->assertInstanceOf(GetPaginatedClientRequest::class, $request);
        $this->assertEquals(ClientQuery::EMBED_ORGANIZATION.','.ClientQuery::EMBED_ONBOARDING, $request->query()->get('embed'));
    }

    /** @test */
    public function create_supports_legacy_embed_flags()
    {
        $request = GetPaginatedClientRequestFactory::new()
            ->withQuery([
                'embedOrganization' => true,
                'embedOnboarding' => true,
            ])
            ->create();

        $this->assertInstanceOf(GetPaginatedClientRequest::class, $request);
        $this->assertEquals(ClientQuery::EMBED_ORGANIZATION.','.ClientQuery::EMBED_ONBOARDING, $request->query()->get('embed'));
    }

    /** @test */
    public function create_returns_paginated_client_request_object_with_minimal_data()
    {
        $request = GetPaginatedClientRequestFactory::new()
            ->create();

        $this->assertInstanceOf(GetPaginatedClientRequest::class, $request);
    }

    /** @test */
    public function create_returns_paginated_client_request_object_with_partial_data()
    {
        $request = GetPaginatedClientRequestFactory::new()
            ->withQuery([
                'limit' => 25,
                'embed' => ['organization'],
            ])
            ->create();

        $this->assertInstanceOf(GetPaginatedClientRequest::class, $request);
        $this->assertEquals(ClientQuery::EMBED_ORGANIZATION, $request->query()->get('embed'));
    }
}

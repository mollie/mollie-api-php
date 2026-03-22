<?php

namespace Tests\Factories;

use Mollie\Api\Factories\GetClientRequestFactory;
use Mollie\Api\Http\Requests\GetClientRequest;
use Mollie\Api\Types\ClientQuery;
use PHPUnit\Framework\TestCase;

class GetClientRequestFactoryTest extends TestCase
{
    private const CLIENT_ID = 'org_12345';

    /** @test */
    public function create_returns_client_request_object_with_full_data()
    {
        $request = GetClientRequestFactory::new(self::CLIENT_ID)
            ->withQuery([
                'embed' => ['organization', 'onboarding'],
            ])
            ->create();

        $this->assertInstanceOf(GetClientRequest::class, $request);
        $this->assertEquals(ClientQuery::EMBED_ORGANIZATION.','.ClientQuery::EMBED_ONBOARDING, $request->query()->get('embed'));
    }

    /** @test */
    public function create_supports_legacy_embed_flags()
    {
        $request = GetClientRequestFactory::new(self::CLIENT_ID)
            ->withQuery([
                'embedOrganization' => true,
                'embedOnboarding' => true,
            ])
            ->create();

        $this->assertInstanceOf(GetClientRequest::class, $request);
        $this->assertEquals(ClientQuery::EMBED_ORGANIZATION.','.ClientQuery::EMBED_ONBOARDING, $request->query()->get('embed'));
    }

    /** @test */
    public function create_returns_client_request_object_with_minimal_data()
    {
        $request = GetClientRequestFactory::new(self::CLIENT_ID)
            ->create();

        $this->assertInstanceOf(GetClientRequest::class, $request);
    }

    /** @test */
    public function create_returns_client_request_object_with_partial_data()
    {
        $request = GetClientRequestFactory::new(self::CLIENT_ID)
            ->withQuery([
                'embed' => ['organization'],
            ])
            ->create();

        $this->assertInstanceOf(GetClientRequest::class, $request);
        $this->assertEquals(ClientQuery::EMBED_ORGANIZATION, $request->query()->get('embed'));
    }
}

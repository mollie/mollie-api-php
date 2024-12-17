<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetPaginatedProfilesRequest;
use Mollie\Api\Resources\Profile;
use Mollie\Api\Resources\ProfileCollection;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\Fixtures\SequenceMockResponse;

class GetPaginatedProfilesRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_paginated_profiles()
    {
        $client = new MockClient([
            GetPaginatedProfilesRequest::class => new MockResponse(200, 'profile-list'),
        ]);

        $request = new GetPaginatedProfilesRequest;

        /** @var ProfileCollection */
        $profiles = $client->send($request);

        $this->assertTrue($profiles->getResponse()->successful());

        foreach ($profiles as $profile) {
            $this->assertInstanceOf(Profile::class, $profile);
            $this->assertEquals('profile', $profile->resource);
        }
    }

    /** @test */
    public function it_can_iterate_over_profiles()
    {
        $client = new MockClient([
            GetPaginatedProfilesRequest::class => new MockResponse(200, 'profile-list'),
            DynamicGetRequest::class => new SequenceMockResponse(
                new MockResponse(200, 'profile-list'),
                new MockResponse(200, 'empty-list', 'profiles'),
            ),
        ]);

        $request = (new GetPaginatedProfilesRequest)->useIterator();

        /** @var ProfileCollection */
        $profiles = $client->send($request);
        $this->assertTrue($profiles->getResponse()->successful());

        foreach ($profiles as $profile) {
            $this->assertInstanceOf(Profile::class, $profile);
        }

        $client->assertSentCount(3);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetPaginatedProfilesRequest;

        $this->assertEquals('profiles', $request->resolveResourcePath());
    }
}

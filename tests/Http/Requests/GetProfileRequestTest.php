<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\GetProfileRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Profile;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class GetProfileRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_profile()
    {
        $client = new MockClient([
            GetProfileRequest::class => new MockResponse(200, 'profile'),
        ]);

        $request = new GetProfileRequest('pfl_v9hTwCvYqw');

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());

        /** @var Profile */
        $profile = $response->toResource();

        $this->assertInstanceOf(Profile::class, $profile);
        $this->assertEquals('profile', $profile->resource);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetProfileRequest('pfl_v9hTwCvYqw');

        $this->assertEquals(
            'profiles/pfl_v9hTwCvYqw',
            $request->resolveResourcePath()
        );
    }
}

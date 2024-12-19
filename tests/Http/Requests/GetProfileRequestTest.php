<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetProfileRequest;
use Mollie\Api\Resources\Profile;
use PHPUnit\Framework\TestCase;

class GetProfileRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_profile()
    {
        $client = new MockMollieClient([
            GetProfileRequest::class => MockResponse::ok('profile'),
        ]);

        $request = new GetProfileRequest('pfl_v9hTwCvYqw');

        /** @var Profile */
        $profile = $client->send($request);

        $this->assertTrue($profile->getResponse()->successful());
        $this->assertInstanceOf(Profile::class, $profile);
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

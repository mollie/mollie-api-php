<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\CreateProfileRequest;
use Mollie\Api\Resources\Profile;
use PHPUnit\Framework\TestCase;

class CreateProfileRequestTest extends TestCase
{
    /** @test */
    public function it_can_create_profile()
    {
        $client = new MockMollieClient([
            CreateProfileRequest::class => MockResponse::created('profile'),
        ]);

        $request = new CreateProfileRequest('Test profile', 'https://example.org', 'test@example.org', 'en_US', '+31612345678');

        /** @var Profile */
        $profile = $client->send($request);

        $this->assertTrue($profile->getResponse()->successful());
        $this->assertInstanceOf(Profile::class, $profile);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new CreateProfileRequest('Test profile', 'https://example.org', 'test@example.org', 'en_US', '+31612345678');

        $this->assertEquals('profiles', $request->resolveResourcePath());
    }
}

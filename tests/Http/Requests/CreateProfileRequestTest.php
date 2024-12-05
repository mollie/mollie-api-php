<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Payload\CreateProfilePayload;
use Mollie\Api\Http\Requests\CreateProfileRequest;
use Mollie\Api\Http\Response;
use Mollie\Api\Resources\Profile;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;
use Tests\TestCase;

class CreateProfileRequestTest extends TestCase
{
    /** @test */
    public function it_can_create_profile()
    {
        $client = new MockClient([
            CreateProfileRequest::class => new MockResponse(201, 'profile'),
        ]);

        $payload = new CreateProfilePayload(
            'Test profile',
            'https://example.org',
            'test@example.org',
            'en_US',
            '+31612345678'
        );

        $request = new CreateProfileRequest($payload);

        /** @var Response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
        $this->assertInstanceOf(Profile::class, $response->toResource());
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new CreateProfileRequest(new CreateProfilePayload(
            'Test profile',
            'https://example.org',
            'test@example.org',
            'en_US',
            '+31612345678'
        ));

        $this->assertEquals('profiles', $request->resolveResourcePath());
    }
}

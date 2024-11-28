<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Http\Requests\CreateProfileRequest;
use Mollie\Api\Http\Requests\DeleteProfileRequest;
use Mollie\Api\Http\Requests\GetProfileRequest;
use Mollie\Api\Http\Requests\GetPaginatedProfilesRequest;
use Mollie\Api\Http\Requests\UpdateProfileRequest;
use Mollie\Api\Resources\Profile;
use Mollie\Api\Resources\ProfileCollection;
use PHPUnit\Framework\TestCase;
use Tests\Fixtures\MockClient;
use Tests\Fixtures\MockResponse;

class ProfileEndpointCollectionTest extends TestCase
{
    /** @test */
    public function create_test()
    {
        $client = new MockClient([
            CreateProfileRequest::class => new MockResponse(201, 'profile'),
        ]);

        /** @var Profile $profile */
        $profile = $client->profiles->create([
            'name' => 'My Test Profile',
            'website' => 'https://example.org',
            'email' => 'info@example.org',
            'phone' => '+31612345678',
            'categoryCode' => 5399,
        ]);

        $this->assertProfile($profile);
    }

    /** @test */
    public function get_test()
    {
        $client = new MockClient([
            GetProfileRequest::class => new MockResponse(200, 'profile'),
        ]);

        /** @var Profile $profile */
        $profile = $client->profiles->get('pfl_v9hTwCvYqw');

        $this->assertProfile($profile);
    }

    /** @test */
    public function get_current_test()
    {
        $client = new MockClient([
            GetProfileRequest::class => new MockResponse(200, 'current-profile'),
        ]);

        /** @var Profile $profile */
        $profile = $client->profiles->getCurrent();

        $this->assertProfile($profile);
    }

    /** @test */
    public function update_test()
    {
        $client = new MockClient([
            UpdateProfileRequest::class => new MockResponse(200, 'profile'),
        ]);

        /** @var Profile $profile */
        $profile = $client->profiles->update('pfl_v9hTwCvYqw', [
            'name' => 'Updated Profile Name',
            'website' => 'https://updated-example.org',
        ]);

        $this->assertProfile($profile);
    }

    /** @test */
    public function delete_test()
    {
        $client = new MockClient([
            DeleteProfileRequest::class => new MockResponse(204),
        ]);

        $client->profiles->delete('pfl_v9hTwCvYqw');

        // Test passes if no exception is thrown
        $this->assertTrue(true);
    }

    /** @test */
    public function page_test()
    {
        $client = new MockClient([
            GetPaginatedProfilesRequest::class => new MockResponse(200, 'profile-list'),
        ]);

        /** @var ProfileCollection $profiles */
        $profiles = $client->profiles->page();

        $this->assertInstanceOf(ProfileCollection::class, $profiles);
        $this->assertGreaterThan(0, $profiles->count());
        $this->assertGreaterThan(0, count($profiles));

        foreach ($profiles as $profile) {
            $this->assertProfile($profile);
        }
    }

    /** @test */
    public function iterator_test()
    {
        $client = new MockClient([
            GetPaginatedProfilesRequest::class => new MockResponse(200, 'profile-list'),
        ]);

        foreach ($client->profiles->iterator() as $profile) {
            $this->assertInstanceOf(Profile::class, $profile);
            $this->assertProfile($profile);
        }
    }

    protected function assertProfile(Profile $profile)
    {
        $this->assertInstanceOf(Profile::class, $profile);
        $this->assertEquals('profile', $profile->resource);
        $this->assertNotEmpty($profile->id);
        $this->assertNotEmpty($profile->mode);
        $this->assertNotEmpty($profile->name);
        $this->assertNotEmpty($profile->website);
        $this->assertNotEmpty($profile->email);
        $this->assertNotEmpty($profile->phone);
        $this->assertNotEmpty($profile->businessCategory);
        $this->assertNotEmpty($profile->status);
        $this->assertNotEmpty($profile->createdAt);
        $this->assertNotEmpty($profile->_links);
    }
}

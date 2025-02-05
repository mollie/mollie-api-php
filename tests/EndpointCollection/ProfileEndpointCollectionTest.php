<?php

namespace Tests\EndpointCollection;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\CreateProfileRequest;
use Mollie\Api\Http\Requests\DeleteProfileRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\GetCurrentProfileRequest;
use Mollie\Api\Http\Requests\GetPaginatedProfilesRequest;
use Mollie\Api\Http\Requests\GetProfileRequest;
use Mollie\Api\Http\Requests\UpdateProfileRequest;
use Mollie\Api\Resources\Profile;
use Mollie\Api\Resources\ProfileCollection;
use PHPUnit\Framework\TestCase;

class ProfileEndpointCollectionTest extends TestCase
{
    /** @test */
    public function create()
    {
        $client = new MockMollieClient([
            CreateProfileRequest::class => MockResponse::created('profile'),
        ]);

        /** @var Profile $profile */
        $profile = $client->profiles->create([
            'name' => 'My Test Profile',
            'website' => 'https://example.org',
            'email' => 'info@example.org',
            'phone' => '+31612345678',
            'businessCategory' => 'test',
        ]);

        $this->assertProfile($profile);
    }

    /** @test */
    public function get()
    {
        $client = new MockMollieClient([
            GetProfileRequest::class => MockResponse::ok('profile'),
        ]);

        /** @var Profile $profile */
        $profile = $client->profiles->get('pfl_v9hTwCvYqw');

        $this->assertProfile($profile);
    }

    /** @test */
    public function get_current()
    {
        $client = new MockMollieClient([
            GetCurrentProfileRequest::class => MockResponse::ok('current-profile'),
        ]);

        /** @var Profile $profile */
        $profile = $client->profiles->getCurrent();

        $this->assertProfile($profile);
    }

    /** @test */
    public function update()
    {
        $client = new MockMollieClient([
            UpdateProfileRequest::class => MockResponse::ok('profile'),
        ]);

        /** @var Profile $profile */
        $profile = $client->profiles->update('pfl_v9hTwCvYqw', [
            'name' => 'Updated Profile Name',
            'website' => 'https://updated-example.org',
        ]);

        $this->assertProfile($profile);
    }

    /** @test */
    public function delete()
    {
        $client = new MockMollieClient([
            DeleteProfileRequest::class => MockResponse::noContent(),
        ]);

        $client->profiles->delete('pfl_v9hTwCvYqw');

        // Test passes if no exception is thrown
        $this->assertTrue(true);
    }

    /** @test */
    public function page()
    {
        $client = new MockMollieClient([
            GetPaginatedProfilesRequest::class => MockResponse::ok('profile-list'),
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
    public function iterator()
    {
        $client = new MockMollieClient([
            GetPaginatedProfilesRequest::class => MockResponse::ok('profile-list'),
            DynamicGetRequest::class => MockResponse::ok('empty-list', 'profiles'),
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

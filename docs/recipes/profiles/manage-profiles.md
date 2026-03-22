# Manage Profiles

How to create, update, list, and delete profiles using the Mollie API.

## Create a Profile

```php
use Mollie\Api\Http\Requests\CreateProfileRequest;

try {
    // Create a new profile
    $profile = $mollie->send(
        new CreateProfileRequest([
            'name' => 'My Website Name',
            'website' => 'https://www.mywebsite.com',
            'email' => 'info@mywebsite.com',
            'phone' => '+31208202070',
            'businessCategory' => 'MARKETPLACES',
            'mode' => 'live'
        ])
    );

    echo "Profile created: {$profile->name}\n";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## List Profiles

```php
use Mollie\Api\Http\Requests\GetPaginatedProfilesRequest;

try {
    // List all profiles
    $response = $mollie->send(new GetPaginatedProfilesRequest);

    foreach ($response as $profile) {
        echo "Profile {$profile->id}:\n";
        echo "- Name: {$profile->name}\n";
        echo "- Website: {$profile->website}\n";
        echo "- Mode: {$profile->mode}\n";
        echo "- Status: {$profile->status}\n\n";
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## Update a Profile

```php
use Mollie\Api\Http\Requests\UpdateProfileRequest;

try {
    // Update an existing profile
    $profile = $mollie->send(
        new UpdateProfileRequest(
            id: 'pfl_v9hTwCvYqw',
            name: 'Updated Website Name',
            website: 'https://www.updated-website.com',
            email: 'info@updated-website.com',
            phone: '+31208202071',
            businessCategory: 'MARKETPLACES'
        )
    );

    echo "Profile updated: {$profile->name}\n";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## Delete a Profile

```php
use Mollie\Api\Http\Requests\DeleteProfileRequest;

try {
    // Delete a profile
    $mollie->send(
        new DeleteProfileRequest(
            profileId: 'pfl_v9hTwCvYqw'
        )
    );

    echo "Profile deleted\n";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## The Response

```php
$profile->id;               // "pfl_v9hTwCvYqw"
$profile->mode;            // "live" or "test"
$profile->name;            // "My Website Name"
$profile->website;         // "https://www.mywebsite.com"
$profile->email;           // "info@mywebsite.com"
$profile->phone;           // "+31208202070"
$profile->businessCategory; // "MARKETPLACES"
$profile->status;          // "verified", "unverified"
$profile->review;          // Object containing review status (optional)
$profile->createdAt;       // "2024-02-24T12:13:14+00:00"
```

## Additional Notes

- OAuth access token is required to manage profiles
- A profile represents your business or website
- Business categories define the type of products/services you offer
# Create a Client Link

How to create a client link to onboard new merchants to Mollie through your app.

## The Code

```php
use Mollie\Api\Http\Data\Owner;
use Mollie\Api\Resources\ClientLink;
use Mollie\Api\Http\Data\OwnerAddress;
use Mollie\Api\Http\Requests\CreateClientLinkRequest;

try {
    // Create a client link for a new merchant
    /** @var ClientLink $clientLink */
    $clientLink = $mollie->send(
        new CreateClientLinkRequest(
            owner: new Owner(
                email: 'merchant@example.com',
                givenName: 'John',
                familyName: 'Doe',
                locale: 'en_US'
            ),
            organizationName: 'Example Store',
            address: new OwnerAddress(
                countryCode: 'NL',
                streetAndNumber: 'Keizersgracht 313',
                postalCode: '1016 EE',
                city: 'Amsterdam'
            ),
            registrationNumber: '30204462',
            vatNumber: 'NL123456789B01'
        )
    );

    // Generate the redirect URL for the merchant
    $redirectUrl = $clientLink->getRedirectUrl(
        clientId: 'app_j9Pakf56Ajta6Y65AkdTtAv',
        state: bin2hex(random_bytes(8)),  // Random state to prevent CSRF
        prompt: 'force',  // Always show login screen
        scopes: [
            'onboarding.read',
            'onboarding.write'
        ]
    );

    // Redirect the merchant to complete their onboarding
    header('Location: ' . $redirectUrl, true, 303);
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## The Response

```php
$clientLink->id;          // "csr_wJPGBj7sFr"
$clientLink->resource;    // "client-link"
$clientLink->status;      // "pending"
$clientLink->createdAt;   // "2024-02-24T12:13:14+00:00"
$clientLink->expiresAt;   // "2024-02-25T12:13:14+00:00"
```

## Additional Notes

- Client links are used to onboard new merchants to Mollie through your app
- The link expires after 24 hours
- Required merchant information:
  - Owner details (name, email, locale)
  - Organization details (name, address)
  - Registration number (Chamber of Commerce number)
  - VAT number (if applicable)
- The `state` parameter should be:
  - Random and unique
  - Stored in your session
  - Verified when the merchant returns to prevent CSRF attacks
- Available scopes:
  - `onboarding.read`: View onboarding status
  - `onboarding.write`: Update onboarding information
- The merchant will need to:
  1. Create a Mollie account or log in
  2. Connect their account to your app
  3. Complete the onboarding process
- You can track the onboarding status through the OAuth APIs

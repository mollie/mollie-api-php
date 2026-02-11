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
- Available scopes (see [Permissions](https://docs.mollie.com/docs/permissions)):
  - `balances.read`: View the merchant's balances information
  - `balance-transfers.read`: View the merchant's balance transfers
  - `balance-transfers.write`: Create balance transfers for the merchant
  - `customers.read`: View the merchant's customers
  - `customers.write`: Manage the merchant's customers
  - `external-accounts.read`: View the merchant's external accounts
  - `external-accounts.write`: Manage the merchant's external accounts
  - `invoices.read`: View the merchant's invoices
  - `mandates.read`: View the merchant's mandates
  - `mandates.write`: Manage the merchant's mandates
  - `onboarding.read`: View the merchant's onboarding status
  - `onboarding.write`: Submit onboarding data for the merchant
  - `orders.read`: View the merchant's orders
  - `orders.write`: Manage the merchant's orders
  - `organizations.read`: View the merchant's organizational details
  - `organizations.write`: Change the merchant's organizational details
  - `payment-links.read`: View the merchant's payment links
  - `payment-links.write`: Create payment links for the merchant
  - `payments.read`: View the merchant's payments, chargebacks and payment methods
  - `payments.write`: Create payments for the merchant (added to the merchant's balance)
  - `persons.read`: View the merchant's persons and stakeholders
  - `persons.write`: Manage the merchant's persons and stakeholders
  - `profiles.read`: View the merchant's website profiles
  - `profiles.write`: Manage the merchant's website profiles
  - `refunds.read`: View the merchant's refunds
  - `refunds.write`: Create or cancel refunds
  - `settlements.read`: View the merchant's settlements
  - `shipments.read`: View the merchant's order shipments
  - `shipments.write`: Manage the merchant's order shipments
  - `subscriptions.read`: View the merchant's subscriptions
  - `subscriptions.write`: Manage the merchant's subscriptions
  - `terminals.read`: View the merchant's point-of-sale terminals
  - `terminals.write`: Manage the merchant's point-of-sale terminals
- The merchant will need to:
  1. Create a Mollie account or log in
  2. Connect their account to your app
  3. Complete the onboarding process
- You can track the onboarding status through the OAuth APIs

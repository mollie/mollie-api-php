# List Account Capabilities

How to retrieve the capabilities of a Mollie account using OAuth.

## The Code

```php
use Mollie\Api\Http\Requests\ListCapabilitiesRequest;
use Mollie\Api\Http\Requests\GetCapabilityRequest;

try {
    // Initialize the Mollie client with your OAuth access token
    $mollie = new \Mollie\Api\MollieApiClient();
    $mollie->setAccessToken('access_xxx');

    // List all capabilities
    $capabilities = $mollie->send(
        new ListCapabilitiesRequest()
    );

    foreach ($capabilities as $capability) {
        echo "Capability: {$capability->name}\n";
        echo "- Status: {$capability->status}\n";
        echo "- Description: {$capability->description}\n\n";
    }

    // Get a specific capability
    $capability = $mollie->send(
        new GetCapabilityRequest(
            id: 'payments'
        )
    );

    echo "Payment capability status: {$capability->status}\n";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## The Response

```php
$capability->id;          // "payments"
$capability->name;        // "Payments"
$capability->description; // "Accept payments from your customers"
$capability->status;      // "active", "pending", "inactive"
```

## Available Capabilities

- `payments` - Accept payments from your customers
- `refunds` - Refund payments to your customers
- `settlements` - Receive settlements in your bank account
- `chargebacks` - Handle chargebacks from your customers
- `onboarding` - Complete onboarding to activate your account
- `organizations` - Create and manage organizations

## Additional Notes

- You need an OAuth access token to access capabilities
- The status indicates whether a capability is available for use:
  - `active`: The capability is enabled and ready to use
  - `pending`: The capability is being reviewed or requires additional information
  - `inactive`: The capability is disabled or not available
- Some capabilities may require additional verification or documentation
- Capabilities vary by country and account type
- Check capabilities before using certain features to ensure they are available

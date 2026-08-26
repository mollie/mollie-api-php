# List Account Capabilities

How to retrieve the capabilities of a Mollie account using OAuth.

## The Code

```php
use Mollie\Api\Http\Requests\ListCapabilitiesRequest;
use Mollie\Api\Http\Requests\GetCapabilityRequest;
use Mollie\Api\Types\CapabilityStatus;

try {
    // Initialize the Mollie client with your OAuth access token
    $mollie = new \Mollie\Api\MollieApiClient();
    $mollie->setAccessToken('access_xxx');

    // List all capabilities
    $capabilities = $mollie->send(
        new ListCapabilitiesRequest()
    );

    foreach ($capabilities as $capability) {
        $status = $capability->status instanceof CapabilityStatus
            ? $capability->status->value
            : $capability->status;

        echo "Capability: {$capability->name}\n";
        echo "- Status: {$status}\n";

        if ($capability->statusReason !== null) {
            echo "- Status reason: {$capability->statusReason}\n";
        }

        echo "\n";
    }

    // Get a specific capability
    $capability = $mollie->send(
        new GetCapabilityRequest(
            name: 'payments'
        )
    );

    $status = $capability->status instanceof CapabilityStatus
        ? $capability->status->value
        : $capability->status;

    echo "Payment capability status: {$status}\n";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## The Response

```php
$capability->name;         // "payments"
$capability->requirements; // Requirements that still need attention
$capability->status;       // CapabilityStatus case, or an unknown raw string
$capability->statusReason; // Status reason, or null when no reason applies
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
  - `unrequested`: The capability has not been requested
  - `enabled`: The capability is enabled and ready to use
  - `pending`: The capability is being reviewed or requires additional information
  - `disabled`: The capability is disabled or not available
- Some capabilities may require additional verification or documentation
- Capabilities vary by country and account type
- Check capabilities before using certain features to ensure they are available

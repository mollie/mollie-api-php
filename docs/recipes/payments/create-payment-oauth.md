# Create a Payment with OAuth

How to create a payment using OAuth authentication with the Mollie API.

## The Code

```php
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CreatePaymentRequest;
use Mollie\Api\Http\Requests\GetPaginatedProfilesRequest;

try {
    // Initialize the Mollie client with your OAuth access token
    $mollie = new \Mollie\Api\MollieApiClient();
    $mollie->setAccessToken('access_xxx');

    // Get the first available profile since OAuth tokens don't belong to a specific profile
    $profiles = $mollie->send(new GetPaginatedProfilesRequest);

    $profile = $profiles[0]; // Select the correct profile for this merchant

    // Generate a unique order ID
    $orderId = time();

    // Create the payment
    $payment = $mollie->send(
        new CreatePaymentRequest(
            profileId: $profile->id, // Required when using OAuth
            description: "Order #{$orderId}",
            amount: new Money(currency: 'EUR', value: '10.00'),
            redirectUrl: 'https://example.com/return.php?order_id=' . $orderId,
            cancelUrl: 'https://example.com/cancel.php',
            webhookUrl: 'https://example.com/webhook.php',
            metadata: ['order_id' => $orderId]
        )
    );

    // Redirect the customer to complete the payment
    header('Location: ' . $payment->getCheckoutUrl(), true, 303);
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## The Response

```php
$payment->id;                // "tr_7UhSN1zuXS"
$payment->status;           // "open"
$payment->amount->currency; // "EUR"
$payment->amount->value;    // "10.00"
$payment->description;      // "Order #1234"
$payment->metadata;         // Object containing order_id
$payment->profileId;        // "pfl_v9hTwCvYqw"
$payment->createdAt;        // "2024-02-24T12:13:14+00:00"
```

## Additional Notes

- OAuth access tokens don't belong to a specific profile, so you need to specify the `profileId` parameter
- Get the profile ID by listing the available profiles with `GetPaginatedProfilesRequest`
- OAuth tokens are required for certain features like routing payments
- Make sure to handle the webhook to process payment status updates
- Store your OAuth tokens securely and refresh them when needed

# Manage Subscriptions

How to create and manage subscriptions using the Mollie API.

## Create a Subscription

```php
use Mollie\Api\Http\Requests\CreateSubscriptionRequest;

try {
    // Create a subscription for a customer
    $subscription = $mollie->send(
        new CreateSubscriptionRequest(
            customerId: 'cst_8wmqcHMN4U',
            parameters: [
                'amount' => [
                    'value' => '10.00',
                    'currency' => 'EUR'
                ],
                'interval' => '1 month',
                'description' => 'Monthly subscription',
                'webhookUrl' => 'https://example.com/webhook',
                'metadata' => [
                    'subscription_id' => time()
                ]
            ]
        )
    );

    echo "Subscription status: {$subscription->status}\n";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## List Subscriptions

```php
use Mollie\Api\Http\Requests\GetPaginatedSubscriptionsRequest;

try {
    // List all subscriptions for a customer
    $response = $mollie->send(
        new GetPaginatedSubscriptionsRequest(
            customerId: 'cst_8wmqcHMN4U'
        )
    );

    foreach ($response as $subscription) {
        echo "Subscription {$subscription->id}:\n";
        echo "- Status: {$subscription->status}\n";
        echo "- Amount: {$subscription->amount->currency} {$subscription->amount->value}\n";
        echo "- Times: {$subscription->times}\n";
        echo "- Interval: {$subscription->interval}\n";
        echo "- Next payment: {$subscription->nextPaymentDate}\n\n";
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## Cancel a Subscription

```php
use Mollie\Api\Http\Requests\CancelSubscriptionRequest;

try {
    // Cancel a subscription
    $mollie->send(
        new CancelSubscriptionRequest(
            customerId: 'cst_8wmqcHMN4U',
            subscriptionId: 'sub_rVKGtNd6s3'
        )
    );

    echo "Subscription canceled\n";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## The Response

```php
$subscription->id;              // "sub_rVKGtNd6s3"
$subscription->customerId;     // "cst_8wmqcHMN4U"
$subscription->mode;           // "live" or "test"
$subscription->createdAt;      // "2024-02-24T12:13:14+00:00"
$subscription->status;         // "active", "pending", "canceled", "suspended", "completed"
$subscription->amount;         // Object containing amount and currency
$subscription->times;          // 12 (optional)
$subscription->timesRemaining; // 4 (optional)
$subscription->interval;       // "1 month"
$subscription->startDate;      // "2024-02-24"
$subscription->nextPaymentDate; // "2024-03-24"
$subscription->description;    // "Monthly subscription"
$subscription->method;         // null or payment method
$subscription->webhookUrl;     // "https://example.com/webhook"
$subscription->metadata;       // Object containing custom metadata
```

## Additional Notes

- The customer must have a valid mandate for recurring payments
- Subscriptions can be created with various intervals (e.g., "1 month", "3 months")
- The webhook is called for every subscription payment
- Subscription status can be:
  - `pending`: Waiting for a valid mandate
  - `active`: The subscription is active
  - `canceled`: The subscription is canceled
  - `suspended`: The subscription is suspended
  - `completed`: The subscription has ended

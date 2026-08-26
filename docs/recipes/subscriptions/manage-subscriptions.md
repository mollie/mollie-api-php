# Manage Subscriptions

How to create and manage subscriptions using the Mollie API.

## Create a Subscription

```php
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CreateSubscriptionRequest;
use Mollie\Api\Types\SubscriptionStatus;

try {
    // Create a subscription for a customer
    $subscription = $mollie->send(
        new CreateSubscriptionRequest(
            customerId: 'cst_8wmqcHMN4U',
            amount: Money::euro('10.00'),
            interval: '1 month',
            description: 'Monthly subscription',
            webhookUrl: 'https://example.com/webhook',
            metadata: ['subscription_id' => time()]
        )
    );

    // A SubscriptionStatus case for values this release knows, the raw string
    // for anything Mollie adds later. Never interpolate the property itself:
    // a backed enum has no string conversion and throws.
    $status = $subscription->status instanceof SubscriptionStatus
        ? $subscription->status->value
        : $subscription->status ?? 'unknown';

    echo "Subscription status: {$status}\n";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## List Subscriptions

```php
use Mollie\Api\Http\Requests\GetPaginatedSubscriptionsRequest;
use Mollie\Api\Types\SubscriptionStatus;

try {
    // List all subscriptions for a customer
    $response = $mollie->send(
        new GetPaginatedSubscriptionsRequest(
            customerId: 'cst_8wmqcHMN4U'
        )
    );

    foreach ($response as $subscription) {
        $status = $subscription->status instanceof SubscriptionStatus
            ? $subscription->status->value
            : $subscription->status ?? 'unknown';

        echo "Subscription {$subscription->id}:\n";
        echo "- Status: {$status}\n";
        echo "- Amount: {$subscription->amount->currency} {$subscription->amount->value}\n";
        echo '- Times: '.($subscription->times ?? 'unlimited')."\n";
        echo "- Interval: {$subscription->interval}\n";
        echo '- Next payment: '.($subscription->nextPaymentDate ?? 'not scheduled')."\n\n";
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
$subscription->status;         // SubscriptionStatus case, raw string, or null
$subscription->amount;         // Mollie\Api\Http\Data\Money
$subscription->times;          // 12 (or null when the subscription is unlimited)
$subscription->timesRemaining; // 4 (or null when the subscription is unlimited)
$subscription->interval;       // "1 month"
$subscription->startDate;      // "2024-02-24" (or null)
$subscription->nextPaymentDate; // "2024-03-24" (null once the subscription ended)
$subscription->description;    // "Monthly subscription"
$subscription->method;         // null or payment method
$subscription->webhookUrl;     // "https://example.com/webhook"
$subscription->metadata;       // Object containing custom metadata
```

## Additional Notes

- The customer must have a valid mandate for recurring payments
- Subscriptions can be created with various intervals (e.g., "1 month", "3 months")
- The webhook is called for every subscription payment
- `status` is a `SubscriptionStatus` case when the SDK recognises the value and the raw string otherwise, so compare with `isActive()`, `isPending()`, `isCanceled()`, `isSuspended()`, `isCompleted()` or `Utility::equals()` instead of `===`:
  - `SubscriptionStatus::Pending`: Waiting for a valid mandate
  - `SubscriptionStatus::Active`: The subscription is active
  - `SubscriptionStatus::Canceled`: The subscription is canceled
  - `SubscriptionStatus::Suspended`: The subscription is suspended
  - `SubscriptionStatus::Completed`: The subscription has ended

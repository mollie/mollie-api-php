# Retry strategies

The Mollie PHP client automatically retries requests that fail with retryable network errors. You can customize how many retries are performed and how long to wait between attempts by providing a retry strategy.

## Default behavior

- Strategy: `LinearRetryStrategy`
- Default max retries: `5` (in addition to the initial attempt)
- Default delay: linear backoff with a 1000ms increase per attempt
  - Attempt 1 (first retry): 1000ms
  - Attempt 2: 2000ms
  - Attempt 3: 3000ms
  - … up to the configured maximum

If all retries are exhausted, the last `Mollie\Api\Exceptions\RetryableNetworkRequestException` is thrown. Fatal middleware hooks, if configured, run once after retries are exhausted.

## Changing the defaults

To change the retry behavior, provide your own strategy instance to the client:

```php
use Mollie\Api\MollieApiClient;
use Mollie\Api\Http\Retry\LinearRetryStrategy;

$client = new MollieApiClient();

// Example: 2 retries with no delay (useful in tests)
$client->setRetryStrategy(new LinearRetryStrategy(2, 0));

// Example: 3 retries with 500ms linear increase (0.5s, 1.0s, 1.5s)
$client->setRetryStrategy(new LinearRetryStrategy(3, 500));
```

To effectively disable retries, set the max retries to `0`:

```php
$client->setRetryStrategy(new LinearRetryStrategy(0, 0));
```

## Creating your own strategy

Custom strategies implement the `Mollie\Api\Contracts\RetryStrategyContract` interface:

```php
namespace Mollie\Api\Contracts;

interface RetryStrategyContract
{
    // Maximum number of retries after the initial attempt
    public function maxRetries(): int;

    // Delay in milliseconds before performing the given retry attempt
    // $attempt starts at 1 for the first retry
    public function delayBeforeAttemptMs(int $attempt): int;
}
```

### Example: Fixed delay strategy

```php
use Mollie\Api\Contracts\RetryStrategyContract;

class FixedDelayRetryStrategy implements RetryStrategyContract
{
    public function __construct(
        private int $maxRetries = 3,
        private int $delayMs = 1000,
    ) {}

    public function maxRetries(): int
    {
        return max(0, $this->maxRetries);
    }

    public function delayBeforeAttemptMs(int $attempt): int
    {
        // Same delay for every retry
        return max(0, $this->delayMs);
    }
}

// Usage
$client->setRetryStrategy(new FixedDelayRetryStrategy(3, 250));
```

You can implement any retry timing you prefer (e.g., exponential backoff with jitter, capped delays, etc.) as long as you adhere to the contract.

## When retries happen

Retries are performed only for exceptions that are considered retryable by the HTTP layer and wrapped as `Mollie\Api\Exceptions\RetryableNetworkRequestException`. Other exceptions are not retried and will be thrown immediately.


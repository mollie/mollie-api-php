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

## Exponential backoff with 429 / Retry-After

`ExponentialRetryStrategy` does exponential backoff with optional jitter, and additionally retries `TooManyRequestsException` (HTTP 429). When the server returns a numeric `Retry-After` header, that value is honoured instead of the computed delay.

```php
use Mollie\Api\Http\ExponentialRetryStrategy;

$client->setRetryStrategy(new ExponentialRetryStrategy(
    maxRetries: 5,
    baseDelayMs: 500,
    multiplier: 2.0,
    maxDelayMs: 30_000,
    jitter: true,
));
```

Use this strategy when you want graceful handling of 429 rate limits — the linear strategy only retries network errors.

## Creating your own strategy

Custom strategies implement the `Mollie\Api\Contracts\RetryStrategyContract` interface:

```php
namespace Mollie\Api\Contracts;

use Throwable;

interface RetryStrategyContract
{
    // Maximum number of retries after the initial attempt
    public function maxRetries(): int;

    // Whether this exception should be retried
    public function shouldRetry(Throwable $exception): bool;

    // Delay in milliseconds before performing the given retry attempt
    // $attempt starts at 1 for the first retry
    public function delayBeforeAttemptMs(int $attempt, ?Throwable $exception = null): int;
}
```

### Example: Fixed delay strategy

```php
use Mollie\Api\Contracts\RetryStrategyContract;
use Mollie\Api\Exceptions\RetryableNetworkRequestException;
use Throwable;

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

    public function shouldRetry(Throwable $exception): bool
    {
        return $exception instanceof RetryableNetworkRequestException;
    }

    public function delayBeforeAttemptMs(int $attempt, ?Throwable $exception = null): int
    {
        // Same delay for every retry
        return max(0, $this->delayMs);
    }
}

// Usage
$client->setRetryStrategy(new FixedDelayRetryStrategy(3, 250));
```

You can implement any retry timing and retryability rules you prefer (e.g., exponential backoff with jitter, capped delays, retrying 429 responses, etc.) as long as you adhere to the contract.

## When retries happen

Retries are performed only when the configured retry strategy returns `true` from `shouldRetry()`. The default `LinearRetryStrategy` retries `Mollie\Api\Exceptions\RetryableNetworkRequestException` only, matching the v3 default behavior. `ExponentialRetryStrategy` also retries `TooManyRequestsException` for HTTP 429 responses.

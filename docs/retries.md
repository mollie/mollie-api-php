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
use Mollie\Api\Http\LinearRetryStrategy;

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

## Exponential backoff with rate-limit support

`ExponentialRetryStrategy` retries temporary network failures and HTTP 429 responses. It uses exponential backoff with optional full jitter. A `Retry-After` value is honored only when it fits within `maxDelayMs`; otherwise the 429 is thrown immediately. Honored delays receive up to 10% additive jitter, capped at 1000ms.

```php
use Mollie\Api\Http\ExponentialRetryStrategy;

$client->setRetryStrategy(new ExponentialRetryStrategy(
    3,      // max retries
    500,    // base delay in milliseconds
    2.0,    // multiplier
    30000,  // maximum delay budget
    true    // jitter
));
```

The default remains `LinearRetryStrategy`, so existing clients continue to retry only temporary network failures.

### Inspecting rate-limit headers

```php
$rateLimit = $response->rateLimit();

if ($rateLimit !== null) {
    $rateLimit->getPolicy();
    $rateLimit->getRemaining();
    $rateLimit->getRestoreSeconds();
    $rateLimit->getBurst();
    $rateLimit->getQuota();
    $rateLimit->getWindowSeconds();
}
```

Missing or malformed headers return `null`. API exceptions retain their response, so 429 details are also available through `$exception->getResponse()->rateLimit()`.

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
    private int $maxRetries;

    private int $delayMs;

    public function __construct(int $maxRetries = 3, int $delayMs = 1000)
    {
        $this->maxRetries = $maxRetries;
        $this->delayMs = $delayMs;
    }

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

You can implement any retry timing you prefer as long as you adhere to the contract. Strategies using this original contract retain the default exception policy: only `Mollie\Api\Exceptions\RetryableNetworkRequestException` is retried.

To make exception-aware retry decisions, including retries for HTTP 429 responses, opt into the extending `Mollie\Api\Contracts\ConditionalRetryStrategyContract`:

```php
namespace Mollie\Api\Contracts;

use Throwable;

interface ConditionalRetryStrategyContract extends RetryStrategyContract
{
    // Whether the exception should trigger a retry
    public function shouldRetry(Throwable $exception): bool;

    // The exception that triggered the retry is available to calculate the delay
    public function delayBeforeAttemptMs(int $attempt, ?Throwable $exception = null): int;
}
```

Implementing this contract is opt-in. Existing `RetryStrategyContract` implementations do not need to change.

## When retries happen

Strategies implementing the original retry contract, including the default linear strategy, retry only `Mollie\Api\Exceptions\RetryableNetworkRequestException`. Strategies implementing `ConditionalRetryStrategyContract` decide which Mollie exceptions are retried; the exponential strategy also retries budget-compatible `Mollie\Api\Exceptions\TooManyRequestsException` instances.

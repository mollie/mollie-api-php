# Upgrading

- [Upgrading from v3 to v4](#upgrading-from-v3-to-v4)
- [Upgrading from v2 to v3](#upgrading-from-v2-to-v3)

# Upgrading from v3 to v4

v4 is a PHP 8.2+ modernization of `mollie/mollie-api-php`. The public surface is largely the same, but some PHP language-level changes can require manual updates in consuming applications.

## 1. Minimum PHP version

PHP **8.2 or newer** is required. PHP 7.4, 8.0, and 8.1 are no longer supported. The CI matrix is 8.2, 8.3, 8.4.

`composer.json`:

```json
"require": {
    "php": "^8.2"
}
```

## 2. High-impact changes

### 2.1 Type constants → enum cases

All 37 classes under `src/Types/` are now PHP **string-backed enums**. The naming convention shifts from `SCREAMING_SNAKE` constants to `PascalCase` cases.

```php
// v3
if ($payment->status === PaymentStatus::PAID) { /* ... */ }

// v4
use Mollie\Api\Types\PaymentStatus;

if ($payment->status === PaymentStatus::Paid) { /* ... */ }
```

Resource properties holding API status/type values are typed as `EnumName|string` so unknown values from the API don't blow up the SDK:

```php
public PaymentStatus|string $status;
```

The `Mollie\Api\Traits\GetAllConstants` trait has been removed together with this migration. Enums expose their cases natively:

```php
// v3
BusinessCategory::all();

// v4 — generic enum approach
array_column(PaymentStatus::cases(), 'value');
```

The three classes that used the trait in v3 (`BusinessCategory`, `ConnectBalanceTransferCategory`, `SubscriptionStatus`) keep a static `all()` method returning the raw string values, so existing `::all()` calls on them continue to work. If you imported `GetAllConstants` into your own classes, replace it with your own helper or switch to enums.

### 2.2 Resource properties typed (no more `\stdClass`)

Resource fields now have explicit PHP types. **Property names are unchanged** — `$payment->amount->value` and `$payment->amount->currency` still work — but the runtime type is now a value object, not `\stdClass`.

```php
// v3
$payment->amount;            // \stdClass { value: "10.00", currency: "EUR" }
$payment->amount->value;     // "10.00"

// v4
$payment->amount;            // Mollie\Api\Http\Data\Money
$payment->amount->value;     // "10.00" (still works)
$payment->amount->currency;  // "EUR"  (still works)
```

If you rely on `$amount instanceof \stdClass` or pass `$payment->amount` to code that JSON-encodes a `stdClass`, you may need to call `$payment->amount->toArray()` instead.

### 2.3 Value objects are `readonly class`

`Money`, `Address`, `OrderLine`, and other value objects under `src/Http/Data/` are now declared `readonly class`. This means you cannot subclass them with non-readonly children.

```php
// v3
class TaxedMoney extends Money { public string $tax; }

// v4 — must also be readonly, or you'll get a fatal error
final readonly class TaxedMoney extends Money {
    public function __construct(string $currency, string $value, public string $tax) {
        parent::__construct($currency, $value);
    }
}
```

Prefer the new **`Macroable`** extension point instead of subclassing — it lets you add factory methods without inheritance:

```php
use Mollie\Api\Http\Data\Money;

Money::macro('platinum', fn (string $value) => new Money('XAU', $value));

$gold = Money::platinum('1.00');
```

See the [custom-money-factory recipe](docs/recipes/money/custom-factory.md).

## 3. Medium-impact changes

### 3.1 Generic `send()` return type

`MollieApiClient::send()` now uses `@template`-based generics. Static analysers (PHPStan, Psalm, PhpStorm) infer the concrete return type from the request class.

```php
// v3 — manual cast / @var was needed
/** @var Payment $payment */
$payment = $client->send(new GetPaymentRequest('tr_xxx'));

// v4 — inferred automatically
$payment = $client->send(new GetPaymentRequest('tr_xxx'));
// $payment is Mollie\Api\Resources\Payment
```

You can safely delete `@var` annotations and manual casts around `send()` calls.

### 3.2 Constructor signatures use property promotion

Request, exception, and value object constructors now use PHP 8 constructor promotion. **Named arguments are unchanged** — they keep working identically. Positional callers may need to reorder arguments if a constructor was rewritten with a different parameter order.

```php
// v4 — recommended (named args)
new CreatePaymentRequest(
    description: 'Order #1',
    amount: new Money(currency: 'EUR', value: '10.00'),
);
```

Audit any positional constructor calls before upgrading.

### 3.3 `declare(strict_types=1)` everywhere

Every file in the SDK now has `declare(strict_types=1)`. Implicit type coercions at SDK boundaries (passing `int` where `string` is expected, etc.) will throw `TypeError`.

```php
// v3 — int silently coerced
new Money('EUR', 10);

// v4 — TypeError
new Money(currency: 'EUR', value: '10.00');  // pass a string
```

### 3.4 `Macroable` on `Money` changes undefined-method behavior

`Money` (and other value objects using `Macroable`) intercept calls to undefined methods. Instead of PHP's default fatal error, you'll get a `BadMethodCallException` with a clear message. This matters only for code that catches `Error` rather than `Exception` around Money calls.

### 3.5 Custom retry strategies must decide retryable exceptions

`RetryStrategyContract` now includes `shouldRetry(Throwable $exception): bool`, and `delayBeforeAttemptMs()` can receive the exception that triggered the retry:

```php
use Mollie\Api\Exceptions\RetryableNetworkRequestException;
use Throwable;

public function shouldRetry(Throwable $exception): bool
{
    return $exception instanceof RetryableNetworkRequestException;
}

public function delayBeforeAttemptMs(int $attempt, ?Throwable $exception = null): int
{
    return $attempt * 1000;
}
```

If you provide your own retry strategy, add this method before upgrading. The built-in `LinearRetryStrategy` keeps the v3 default behavior for retryable network failures; the new `ExponentialRetryStrategy` can also retry HTTP 429 responses.

# Upgrading from v2 to v3

Welcome to Mollie API PHP v3! This guide will help you smoothly transition from v2 to v3.

The codebase has significant improvements, focusing on modern PHP practices, enhanced type safety, and offers a more intuitive developer experience.

## Breaking Changes

### Deprecations

#### MethodEndpointCollection.allActive()

The method `MethodEndpointCollection.allActive()` has been removed. Use `MethodEndpointCollection.allEnabled()` instead.

#### Order endpoint

Orders: Mollie is deprecating the Order and Shipment endpoints so these have been removed from `mollie-api-php`. The same functionality is now available through the Payment endpoint as well. So, use the Payment endpoint instead.

  - All `/orders/*` endpoints and related classes (`Order*Endpoint`)
  - Removed `MollieApiClient` properties:
    ```php
    $client->orderPayments;  // Removed
    $client->orderRefunds;   // Removed
    $client->orderLines;     // Removed
    $client->shipments;      // Removed
    ```

#### Integration code examples

To prevent misuse the code samples in `/examples` were replaced by markdown "recipes", which can be found in `/docs/recipes`.

### Metadata Type Restriction

In v2, when making API requests, the metadata parameter accepted any type (string, array, object, etc.). In v3, metadata in request payloads is restricted to only accept arrays. Make sure to update your code to provide metadata as arrays when making API requests.

```php
// Before (v2) - Using legacy array approach
$client->payments->create([
    "amount" => [
        "currency" => "EUR",
        "value" => "10.00"
    ],
    "metadata" => "some string"      // Worked in v2
]);

// After (v3) - Using legacy array approach
$client->payments->create([
    "amount" => [
        "currency" => "EUR",
        "value" => "10.00"
    ],
    "metadata" => ["key" => "value"] // Only arrays are accepted in v3
]);

// After (v3) - Using request class
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CreatePaymentRequest;

$request = new CreatePaymentRequest(
    description: "My payment",
    amount: new Money("EUR", "10.00"),
    metadata: ["key" => "value"]     // Only arrays are accepted
);
$payment = $client->send($request);
```

### Class & Method Renames

#### Endpoint Class Changes
| Old Class                    | New Class                              | Method Changes                                             |
|------------------------------|----------------------------------------|------------------------------------------------------------|
| `MethodEndpoint`             | `MethodEndpointCollection`             | `allAvailable()` → `all()`<br>`all()` → `allEnabled()`     |
| `BalanceTransactionEndpoint` | `BalanceTransactionEndpointCollection` | `listFor()` → `pageFor()`<br>`listForId()` → `pageForId()` |
| `CustomerPaymentsEndpoint`   | `CustomerPaymentsEndpointCollection`   | `listFor()` → `pageFor()`<br>`listForId()` → `pageForId()` |
| `MandateEndpoint`            | `MandateEndpointCollection`            | `listFor()` → `pageFor()`<br>`listForId()` → `pageForId()` |
| `PaymentRefundEndpoint`      | `PaymentRefundEndpointCollection`      | `listFor()` → `pageFor()`<br>`listForId()` → `pageForId()` |
| `OnboardingEndpoint`         | `OnboardingEndpointCollection`         | `get()` → `status()`                                       |
| `SubscriptionEndpoint`       | `SubscriptionEndpointCollection`       | `page()` → `allFor()`                                      |

#### Signature Changes
You can now use named parameters for improved readability and flexibility:

```php
// Before (v2)
$mandates = $client->mandates->listFor($customer, 0, 10);

// After (v3)
$mandates = $client->mandates->pageForCustomer(
    $customer,
    from: null,
    limit: 10,
    testmode: false
);
```

### Constant & Collection Changes

- **Streamlined constants** - Redundant prefixes have been removed for a cleaner API:
  ```php
  // Before
  Payment::STATUS_PAID;

  // After
  Payment::PAID;
  ```

- **Simplified SequenceType constants**:
  ```php
  // Before
  SequenceType::SEQUENCETYPE_FIRST;

  // After
  SequenceType::FIRST;
  ```

- **Cleaner collection initialization**:
  ```php
  // Before
  new PaymentCollection(10, $payments);

  // After
  new PaymentCollection($payments);
  ```

### Test Mode Handling

- **Automatic detection** with API keys
- **Explicit parameter** for organization credentials:
  ```php
  // Get payment link in test mode
  $link = $client->paymentLinks->get('pl_123', testmode: true);
  ```

Read the full testing documentation [here](docs/testing.md).

### Removed Collections

- `OrganizationCollection`
- `RouteCollection`

## New Features

### Modern HTTP Handling

#### PSR-18 Support
You can now use any PSR-18 compatible HTTP client:

```php
use Mollie\Api\HttpAdapter\PSR18MollieHttpAdapter;

$adapter = new PSR18MollieHttpAdapter(
    new GuzzleClient(),
    new Psr17Factory(),
    new Psr17Factory()
);

$client = new MollieApiClient($adapter);
```

### Enhanced Request Handling

#### Typed Request Objects
You can now say goodbye to array-based payloads and hello to type-safe request objects:

```php
use Mollie\Api\Http\Requests\CreatePaymentRequest;

$request = new CreatePaymentRequest(
    amount: new Money('EUR', '10.00'),
    description: 'Order 123',
    redirectUrl: 'https://example.com/redirect'
);

$payment = $client->send($request);
```

Read the full request documentation [here](docs/requests.md).

### Collection Improvements

Collections now feature powerful functional methods for more expressive code:

```php
// New methods
$activePayments = $payments->filter(fn($p) => $p->isActive());
$hasRefunds = $payments->contains(fn($p) => $p->hasRefunds());
```

### Method Issuer Contracts

You can now easily manage method issuers with optional contract IDs:

```php
$client->methodIssuers->enable(
    profileId: 'pfl_123',
    methodId: 'voucher',
    issuerId: 'iss_456',
    contractId: 'contract_789'  // Optional
);
```

## Further reading

Usage guides for the PHP client can be found in the [docs](docs). For more information on the Mollie API, check out [the official Mollie docs](https://docs.mollie.com).

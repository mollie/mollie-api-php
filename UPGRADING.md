# Upgrading

- [Upgrading from v3 to v4](#upgrading-from-v3-to-v4)
- [Upgrading from v2 to v3](#upgrading-from-v2-to-v3)

# Upgrading from v3 to v4

Use this guide to upgrade an application from v3 to v4. The public surface is largely the same, but PHP language-level changes require a few manual updates in consuming applications. Work through the sections below before deploying v4.

## 1. Set the minimum PHP version

Set your application's PHP requirement to **8.2 or newer**. Remove support for PHP 7.4, 8.0, and 8.1; v4's CI matrix covers PHP 8.2, 8.3, and 8.4.

`composer.json`:

```json
"require": {
    "php": "^8.2"
}
```

## 2. High-impact changes

### 2.1 Type constants → enum cases

Every API value set under `src/Types/` is now a PHP **string-backed enum**. Only the query helpers (`ClientQuery`, `MandateQuery`, `MethodQuery`, `PaymentIncludesQuery`, `PaymentQuery`, `TerminalPairingCodeQuery`) and `Mollie\Api\Types\Method` (HTTP verbs) remain constant classes. Change each `SCREAMING_SNAKE` constant reference to its `PascalCase` case:

```php
// v3
if ($payment->status === PaymentStatus::PAID) { /* ... */ }

// v4
use Mollie\Api\Types\PaymentStatus;

if ($payment->status === PaymentStatus::Paid) { /* ... */ }
```

When you read a resource property that holds an API status or type, handle it as `EnumName|string`. The `string` fallback keeps unknown API values from breaking your integration:

```php
public PaymentStatus|string $status;
```

Remove imports and uses of the `Mollie\Api\Traits\GetAllConstants` trait. Use the enum cases directly:

```php
// v3
BusinessCategory::all();

// v4 — generic enum approach
array_column(PaymentStatus::cases(), 'value');
```

Keep calling `::all()` on `BusinessCategory`, `ConnectBalanceTransferCategory`, and `SubscriptionStatus`; these enums retain compatibility methods that return their raw string values. If your own classes imported `GetAllConstants`, replace the trait with a local helper or migrate those classes to enums.

If you enumerated constants through reflection, replace that too. On an enum, `ReflectionClass::getConstants()` returns the **case objects**, not their string values, and `defined()` / `constant()` on the old `SCREAMING_SNAKE` names now fail:

```php
// v3
(new ReflectionClass(PaymentMethod::class))->getConstants();   // ['IDEAL' => 'ideal', ...]

// v4 — the same call returns ['Ideal' => PaymentMethod::Ideal, ...] with no error
array_column(PaymentMethod::cases(), 'value');                  // ['ideal', ...] — the replacement
```

`cases()` is the vocabulary this SDK release knows, not an allow-list. Mollie can return a value that is not a case yet; it reaches you as a raw string through the `EnumName|string` property types. `CreatePaymentRequest` accepts a raw string for `method`; `Payment::$method` preserves unknown response values as raw strings. Use the Methods API to learn which methods are enabled on a profile.

`PaymentMethodStatus` and `TerminalPairingCodeStatus` became enums after v4.0.0-beta.2. `PaymentMethodStatus::NOT_REQUESTED` has no case: a method that was never requested has `$method->status === null`. `Method::$status` is required but nullable and has no default. `GetEnabledMethodsRequest` reads it while filtering, so a conformant response must include `status`, using an explicit `null` when the method was not requested; an omitted field is malformed and remains visibly uninitialized.

### 2.2 Resource properties typed (no more `\stdClass`)

Update code that assumes a resource field is an untyped `\stdClass`. **Property names are unchanged** — `$payment->amount->value` and `$payment->amount->currency` still work — but the runtime value is a typed value object.

```php
// v3
$payment->amount;            // \stdClass { value: "10.00", currency: "EUR" }
$payment->amount->value;     // "10.00"

// v4
$payment->amount;            // Mollie\Api\Http\Data\Money
$payment->amount->value;     // "10.00" (still works)
$payment->amount->currency;  // "EUR"  (still works)
```

If your code checks `$amount instanceof \stdClass` or requires an array, update that path and call `$payment->amount->toArray()` where needed. `json_encode($payment->amount)` continues to work because the value object's properties are public.

### 2.3 Value objects are `readonly`: rebuild instead of mutating

`Money`, `Address`, `OrderLine`, and the other classes under `src/Http/Data/` are `readonly class`. Any code that assigned to one of their properties fails with `Cannot modify readonly property`. The place most integrations hit this is `onRequest()` middleware, which runs before the SDK serializes the payload and therefore sees the live value objects. Rebuild the object instead:

```php
use Mollie\Api\Http\Data\Address;

// v3 — worked
$address->email = $this->punycodeEmailDomain($address->email);

// v4 — Cannot modify readonly property; rebuild through fromArray()
$address = Address::fromArray([
    ...$address->toArray(),
    'email' => $this->punycodeEmailDomain($address->email),
]);
```

Subclassing is the second, rarer break:

If you extend `Money`, `Address`, `OrderLine`, or another value object under `src/Http/Data/`, declare the child as `readonly class`; PHP 8.2 does not allow a non-readonly child. Prefer the `Macroable` extension point when you only need to add factory methods.

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

Use the **`Macroable`** extension point instead of subclassing when you need to add factory methods:

```php
use Mollie\Api\Http\Data\Money;

Money::macro('platinum', fn (string $value) => new Money('XAU', $value));

$gold = Money::platinum('1.00');
```

See the [custom-money-factory recipe](docs/recipes/money/custom-factory.md).

### 2.4 Typed properties can be uninitialized

Resource properties are typed in v4. When a response omits a field that has no default, reading it no longer returns `null` as in v3; it throws `Error: Typed property Mollie\Api\Resources\... must not be accessed before initialization`. This is the most common runtime break after upgrading and it is invisible at the call site.

Read fields that a partial or malformed response may omit through `isset()` or `??` at the boundary. Both treat an uninitialized typed property as unset instead of throwing:

```php
// v3
$description = $payment->description;            // null when the response omitted it

// v4
$description = $payment->description ?? null;    // same result, no Error
```

Two caveats. A guard only helps when *your* variable may be null: guarding a value you then pass to a non-nullable parameter moves the failure one line down. The SDK deliberately does **not** default fields the API marks as required (`Payment::$id`, `Organization::$name`, and others): an uninitialized required field means the response was malformed, and hiding it behind `null` would turn a visible failure into wrong data. Where a field has been verified against the API contract, an optional field carries `?T` with a `null` default and a required-but-nullable field carries `?T` without one.

## 3. Medium-impact changes

### 3.1 Generic `send()` return type

Remove manual return-type workarounds around `MollieApiClient::send()`. Its `@template`-based generics let PHPStan infer the concrete return type from the request class.

```php
// v3 — manual cast / @var was needed
/** @var Payment $payment */
$payment = $client->send(new GetPaymentRequest('tr_xxx'));

// v4 — inferred automatically
$payment = $client->send(new GetPaymentRequest('tr_xxx'));
// $payment is Mollie\Api\Resources\Payment
```

Delete the `@var` annotations and manual casts around `send()` calls that only existed to help static analysis.

This holds for wrapped requests too, as long as you use the named methods added after v4.0.0-beta.2. `setHydratableResource(new WrapperResource(...))` returns the request unchanged for static analysis, so `send()` cannot infer the wrapper; `wrapInto()` (and `hydrateInto()` for re-targeting) can:

```php
// inferred: PaymentWrapper
$wrapper = $client->send(
    (new GetPaymentRequest('tr_xxx'))->wrapInto(PaymentWrapper::class)
);

// inferred: RefundsWrapper
$wrapped = $client->send(
    (new DynamicGetRequest($href))
        ->hydrateInto(RefundCollection::class)
        ->wrapInto(RefundsWrapper::class)
);
```

Call request-specific setters first, then `hydrateInto()`, then `wrapInto()` last. Reversing the two named helpers makes static analysis report the hydration target while runtime still returns the wrapper. PHPStan currently honors both the `@phpstan-self-out` and `@psalm-this-out` annotations, and the committed fixture guards their combined inference contract. This repository does not run Psalm, so Psalm behavior is not verified here. PhpStorm does not infer the narrowed type. See [`docs/responses.md`](docs/responses.md#resource-wrappers).

### 3.2 Constructor signatures use property promotion

Review calls to request, exception, and value object constructors. These constructors use PHP 8 property promotion in v4. **Named arguments are unchanged** and continue to work. Reorder positional arguments when a constructor uses a different parameter order.

```php
// v4 — recommended (named args)
new CreatePaymentRequest(
    description: 'Order #1',
    amount: new Money(currency: 'EUR', value: '10.00'),
);
```

Search for positional constructor calls and verify their argument order before upgrading.

### 3.3 Typed signatures: coercion depends on *your* `strict_types`

v4 signatures are fully typed, but `declare(strict_types=1)` is a property of the **calling** file, not of the SDK. A strict declaration in an SDK file does not make calls from your weak-mode file strict.

```php
// consumer file WITHOUT declare(strict_types=1) — the default in most frameworks
new Money('EUR', 10);        // no error; value is coerced to '10' (not '10.00' — Mollie rejects it)

// consumer file WITH declare(strict_types=1)
new Money('EUR', 10);        // TypeError: value must be of type string, int given

// correct in both
new Money(currency: 'EUR', value: '10.00');
```

Audit every SDK boundary for scalar coercions: in weak-mode files they are silent and can produce values the API rejects; in strict-mode files they throw. Do not rely on a `TypeError` to catch bad input unless your own file is strict.

### 3.4 `Macroable` on `Money` changes undefined-method behavior

`Money` (and other value objects using `Macroable`) intercept calls to undefined methods. Instead of PHP's default fatal error, you'll get a `BadMethodCallException` with a clear message. This matters only for code that catches `Error` rather than `Exception` around Money calls.

### 3.5 Custom retry strategies must decide retryable exceptions

Update custom implementations of `RetryStrategyContract` with `shouldRetry(Throwable $exception): bool`. Pass the triggering exception to `delayBeforeAttemptMs()` when you need exception-specific backoff:

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

If you provide your own retry strategy, add this method before upgrading. Keep `LinearRetryStrategy` to preserve the v3 default behavior for retryable network failures, or switch to `ExponentialRetryStrategy` if you also want to retry HTTP 429 responses.

### 3.6 Custom endpoint maps use a class constant

If an undocumented `MollieApiClient` subclass directly mutated the protected static `$endpoints` property, replace that mutation with a protected `ENDPOINTS` constant override:

```php
class CustomMollieApiClient extends MollieApiClient
{
    protected const ENDPOINTS = [
        ...parent::ENDPOINTS,
        'customPayments' => CustomPaymentEndpointCollection::class,
    ];
}
```

### 3.7 Response middleware runs in two phases

`onResponse()` callbacks now always receive the raw SDK `Response`, regardless of priority. Move transforms that need a hydrated resource or collection to `onResolved()`.

Before:

```php
$client->middleware()->onResponse(function ($result) {
    return $result instanceof MethodCollection
        ? $result->filter(fn (Method $method) => $method->status !== null)
        : $result;
}, MiddlewarePriority::LOW);
```

After:

```php
$client->middleware()->onResolved(function ($result) {
    return $result instanceof MethodCollection
        ? $result->filter(fn (Method $method) => $method->status !== null)
        : $result;
}, MiddlewarePriority::LOW);

$client->middleware()->onResponse(function (Response $response): void {
    // Inspect the raw HTTP response.
});
```

Returning `null` or returning without a value preserves the current value in both phases. An `onResponse()` callback may otherwise return only `Mollie\Api\Http\Response`; another return type throws `UnexpectedValueException` immediately. Priorities control order only within their own raw or resolved phase.

# Upgrading from v2 to v3

Use this guide to move your application from v2 to v3. Complete the breaking-change updates before switching the dependency to v3.

After the migration, you can use the v3 APIs for modern PHP practices, stronger type safety, and a more predictable developer experience.

## Breaking Changes

### Deprecations

#### MethodEndpointCollection.allActive()

The method `MethodEndpointCollection.allActive()` has been removed. Use `MethodEndpointCollection.allEnabled()` instead.

#### Order endpoint

Replace Order and Shipment endpoint calls with Payment endpoint calls. Mollie removed the Order and Shipment endpoints from `mollie-api-php`, and the Payment endpoint provides the replacement functionality.

  - All `/orders/*` endpoints and related classes (`Order*Endpoint`)
  - Removed `MollieApiClient` properties:
    ```php
    $client->orderPayments;  // Removed
    $client->orderRefunds;   // Removed
    $client->orderLines;     // Removed
    $client->shipments;      // Removed
    ```

#### Integration code examples

Move any code you copied from `/examples` to the markdown recipes in `/docs/recipes`; v3 uses those recipes as the maintained integration examples.

### Metadata Type Restriction

Change every v2 request that sends metadata as a string or object. Pass metadata as an array in v3 request payloads.

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
Use named parameters to make request calls easier to read and maintain:

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

- **Streamlined constants** - Remove redundant prefixes and use the shorter constant names:
  ```php
  // Before
  Payment::STATUS_PAID;

  // After
  Payment::PAID;
  ```

- **Simplified SequenceType constants** - Use the shorter enum cases:
  ```php
  // Before
  SequenceType::SEQUENCETYPE_FIRST;

  // After
  SequenceType::FIRST;
  ```

- **Cleaner collection initialization** - Pass the items array directly:
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
Configure any PSR-18-compatible HTTP client with the SDK:

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
Replace array-based payloads with type-safe request objects:

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

Use the collection methods below to write more expressive queries:

```php
// New methods
$activePayments = $payments->filter(fn($p) => $p->isActive());
$hasRefunds = $payments->contains(fn($p) => $p->hasRefunds());
```

### Method Issuer Contracts

Manage method issuers by passing optional contract IDs:

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

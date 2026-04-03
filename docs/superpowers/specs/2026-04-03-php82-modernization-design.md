# mollie-api-php v4 — PHP 8.2+ Modernization Design

**Date:** 2026-04-03
**Status:** Approved
**Version:** 4.0.0
**PHP Minimum:** 8.2

## Overview

Major version upgrade of `mollie/mollie-api-php` from v3 (PHP 7.4+) to v4 (PHP 8.2+). This release leverages modern PHP features to improve type safety, developer experience, and static analysis coverage while maintaining forward compatibility with future Mollie API changes.

**GitHub issues resolved:**
- mollie/mollie-api-php#875 — Generic return types on `send()`
- mollie/mollie-api-php#876 — `Money` convenience factories

## 1. Versioning & PHP Baseline

- **Version:** `4.0.0` — the PHP 8.2 floor is a breaking change requiring a new semver major.
- **Branch strategy:** Cut a `3.x` branch from current `master` for maintenance/backports. `master` becomes the v4 development branch. This is a "main is v4" strategy — v3 gets bug fixes on the `3.x` branch.
- **`composer.json`:** `require.php` changes from `^7.4|^8.0` to `^8.2`.
- **CI matrix:** PHP 8.2, 8.3, 8.4.
- **Testing:** Pest v3 (`pestphp/pest: ^3.0`) in `require-dev`. Pest v3 requires PHP 8.2+, aligning with our floor. Existing PHPUnit tests continue to work — migration to Pest syntax is gradual, file-by-file. Paratest is dropped (Pest has `--parallel` built in).
- **Strict types:** `declare(strict_types=1)` added to every file.
- **PHPStan:** Target level 6 initially (up from current 5). Level 8+ is a later milestone once typed resources reveal the real baseline reduction.

## 2. Type System: Enums

All 37 classes in `src/Types/` that represent API domain constants become **string-backed enums**. The naming convention shifts from `SCREAMING_SNAKE` constants to `PascalCase` cases (PHP enum convention).

```php
// Before (v3)
class PaymentStatus {
    public const OPEN = 'open';
    public const PAID = 'paid';
}

// After (v4)
enum PaymentStatus: string {
    case Open = 'open';
    case Paid = 'paid';
}
```

### Resource properties typed to enums

Resource properties that hold API status/type values are typed directly to their enum:

```php
public readonly PaymentStatus $status;
```

The hydrator uses `PaymentStatus::from($json->status)` to cast the API string into the enum. For forward compatibility (unknown values from future API changes), `::tryFrom()` is used — if it returns `null` (unknown value), the property falls into tier 2 (dynamic property) as a raw string, ensuring no data loss and no crashes when Mollie adds new statuses.

### Convenience methods stay

Methods like `$payment->isPaid()` remain — they're useful for readability and IDE discoverability. Internally they compare enums instead of strings.

### Exception: `Method.php` (HTTP verbs)

`src/Types/Method.php` contains HTTP verb constants (`GET`, `POST`, etc.). This is internal plumbing that never surfaces to SDK consumers. It stays as a plain class with string constants — no enum conversion. Only types that SDK consumers interact with become enums.

### Rector migration

The `ConstantToEnumCaseRector` rule handles the rename automatically (e.g., `PaymentStatus::PAID` → `PaymentStatus::Paid`).

## 3. Value Objects: `readonly class` + Constructor Promotion

All 21 classes in `src/Http/Data/` become `readonly class` with constructor promotion.

```php
// Before (v3)
class Money implements Arrayable {
    use ComposableFromArray;
    use HasCurrencyConvenienceMethods;

    public string $currency;
    public string $value;

    public function __construct(string $currency, string $value) {
        $this->currency = $currency;
        $this->value = $value;
    }
}

// After (v4)
readonly class Money implements Arrayable {
    use ComposableFromArray;
    use HasCurrencyConvenienceMethods;
    use Macroable;

    public function __construct(
        public string $currency,
        public string $value,
    ) {}
}
```

### What changes across the 21 files

- `class` → `readonly class`
- Explicit property declarations removed
- Constructor uses promoted parameters
- `toArray()` stays — it's the `Arrayable` contract, still needed for payload serialization

### Enum-typed properties

Value objects that hold a type string become enum-typed where applicable (e.g., `OrderLine::$type` → `OrderLineType` enum).

### Named arguments

With constructor promotion, named arguments become the idiomatic construction pattern:

```php
new Money(currency: 'EUR', value: '10.00');
new Address(streetAndNumber: '123 Main St', city: 'Amsterdam', country: 'NL');
```

### `ComposableFromArray` trait

Still needed — it powers the `from(array $data)` factory method used by the hydrator. Readonly classes work with traits as long as the trait doesn't mutate properties after construction.

### Money convenience factories (resolves #876)

`Money` gains `fromCents()` and `fromFloat()` factory methods to reduce string formatting errors:

```php
// Generic factories
Money::fromCents('EUR', 1000);    // → Money('EUR', '10.00')
Money::fromFloat('EUR', 10.0);    // → Money('EUR', '10.00')

// Per-currency variants (generated)
Money::euroFromCents(1000);
Money::euroFromFloat(10.0);
Money::euro('10.00');             // existing, still works
```

The per-currency methods (`euroFromCents`, `euroFromFloat`, etc.) are generated by a Rector script / code generator from a currency list. The generated `HasCurrencyConvenienceMethods` trait is committed to source — adding a new currency means adding to the list and re-running the generator. CI validates the committed trait matches the generator output.

### Macroable extension point

`Money` (and potentially other value objects) gains a lightweight `Macroable` trait enabling consumers to register custom factory methods without subclassing:

```php
Money::macro('fromBrickMoney', function (BrickMoney $money): Money {
    return new Money(
        currency: $money->getCurrency()->getCurrencyCode(),
        value: (string) $money->getAmount(),
    );
});

$amount = Money::fromBrickMoney($brickMoneyInstance);
```

This keeps third-party money libraries (`brick/money`, `moneyphp/money`) out of the SDK while providing a clean hook for integration. The `Macroable` trait is ~30 lines: `macro()`, `hasMacro()`, `__callStatic()`, `__call()`. It works with `readonly class` because macros create new instances rather than mutating existing ones.

## 4. Request Layer: Constructor Promotion + Named Arguments

All request classes in `src/Http/Requests/` get constructor promotion. Unlike value objects, requests are **not** `readonly class` — some have mutable state via methods like `->test()`.

```php
// Before (v3) — 23 explicit properties + assignment body
class CreatePaymentRequest extends ResourceHydratableRequest implements HasPayload {
    private string $description;
    private Money $amount;
    // ... 20 more properties + 23-line constructor body

// After (v4) — constructor promotion, no body
class CreatePaymentRequest extends ResourceHydratableRequest implements HasPayload {
    use HasJsonPayload;

    protected static string $method = Method::POST;
    protected string $hydratableResource = Payment::class;

    public function __construct(
        private string $description,
        private Money $amount,
        private ?string $redirectUrl = null,
        // ...
        private array $additional = [],
        private bool $includeQrCode = false,
    ) {}
```

### Key decisions

- **Visibility stays `private`** — request properties are internal; consumers pass them via the constructor and `defaultPayload()` serializes them.
- **Union types replace docblock annotations** — `@param array|string|null $method` becomes `private string|array|null $method = null`.
- **`$hydratableResource` gets typed** — `protected $hydratableResource` becomes `protected string $hydratableResource = Payment::class` across all request classes.
- **Factories (`src/Factories/`)** that bridge `array → Request` continue to work — they call the constructor with named arguments.

## 5. Typed Response Resources

Resource classes in `src/Resources/` get typed properties that use value objects from `src/Http/Data/` and enums from `src/Types/`.

```php
// Before (v3)
class Payment extends BaseResource {
    /** @var string */
    public $id;
    /** @var \stdClass */
    public $amount;
    /** @var string */
    public $status = PaymentStatus::OPEN;

// After (v4)
class Payment extends BaseResource {
    public readonly string $id;
    public readonly Money $amount;
    public readonly ?Address $billingAddress;
    public readonly PaymentStatus $status;
    public readonly ?Money $settlementAmount;
    public readonly ?Money $amountRefunded;
    public readonly ?Money $amountRemaining;
```

### Two-tier hydration (forward compatibility)

The SDK must not become a bottleneck for Mollie API evolution. If the API returns fields not yet declared in the SDK, they must still be accessible.

`BaseResource` uses `#[AllowDynamicProperties]` for a two-tier approach:

```php
#[AllowDynamicProperties]
class BaseResource {
    // Tier 1: Declared typed properties → cast via hydrator (enum, value object, scalar)
    // Tier 2: Undeclared properties → set as dynamic properties (stdClass/scalar, like v3)
}
```

**Hydration mechanism:**

1. **Scalars** (`string`, `int`, `bool`) — assigned directly
2. **Backed enums** — `EnumType::from($value)` with `tryFrom()` + fallback for forward compatibility
3. **Value objects** — `ValueObject::from($value)` via existing `ComposableFromArray` trait
4. **Nullable properties** — if JSON key is absent or `null`, property stays `null`
5. **Collections** — `DataCollection<OrderLine>` for arrays of typed objects
6. **Unknown fields** — set as dynamic properties, accessible just like v3

The hydrator uses **reflection** on the resource class to discover property types and dispatch to the right casting strategy. No attributes needed for common cases — the type declaration itself is the mapping instruction.

**`_links` and `_embedded`** stay as `\stdClass` — they're structural HAL metadata, not domain objects. They naturally fall into tier 2.

### Generic return types on `send()` (resolves #875)

`ResourceHydratableRequest` becomes generic over its resource type:

```php
/**
 * @template TResource
 */
abstract class ResourceHydratableRequest extends Request {
    /** @var class-string<TResource>|null */
    protected ?string $hydratableResource = null;
}
```

Each concrete request binds the template:

```php
/** @extends ResourceHydratableRequest<Payment> */
class GetPaymentRequest extends ResourceHydratableRequest { /* ... */ }

/** @extends ResourceHydratableRequest<PaymentCollection> */
class GetPaginatedPaymentsRequest extends ResourceHydratableRequest { /* ... */ }
```

The `send()` method uses a conditional return type:

```php
/**
 * @template TRequest of Request
 * @param TRequest $request
 * @return (TRequest is ResourceHydratableRequest<infer TResource> ? TResource : mixed)
 */
public function send(Request $request)
```

Result: `$client->send(new GetPaymentRequest('tr_xxx'))` infers `Payment` — no `@var`, no casting.

## 6. Exception Improvements

### `TooManyRequestsException` — expose `Retry-After`

```php
class TooManyRequestsException extends ApiException {
    public function __construct(
        Response $response,
        public readonly ?int $retryAfterSeconds,
        string $message,
        int $code,
    ) { /* ... */ }

    public static function fromResponse(Response $response): self {
        $retryAfter = $response->header('Retry-After');
        // ...
    }
}
```

### `ValidationException` — expose all field errors

```php
class ValidationException extends ApiException {
    public function __construct(
        Response $response,
        public readonly string $field,
        public readonly array $errors,
        string $message,
        int $code,
    ) { /* ... */ }

    public function hasError(string $field): bool { /* ... */ }
    public function getError(string $field): ?string { /* ... */ }
}
```

### All exceptions

Constructor promotion across all exceptions in `src/Exceptions/`. Readonly properties make exception data immutable — semantically correct for a fixed point-in-time failure.

## 7. New Features

### `MollieApiClient::fromEnv()`

```php
$client = MollieApiClient::fromEnv();
// Reads MOLLIE_API_KEY or MOLLIE_ACCESS_TOKEN
// Throws MissingAuthenticationException if neither is set
```

### Built-in retry middleware (opt-in)

Adds `RetryMiddleware` to the existing middleware pipeline:

- Retries on `TooManyRequestsException` (429) using `retryAfterSeconds`
- Retries on `RetryableNetworkRequestException` (connection failures, timeouts)
- Exponential backoff with jitter for network errors
- Configurable max attempts (default: 3)
- Does **not** retry on 4xx client errors (validation, auth) — those are permanent failures
- Opt-in, not default: `$client->withRetry(maxAttempts: 3)`

### Typed `MockResponse` factories

```php
// Before — raw JSON arrays
MockResponse::ok(['resource' => 'payment', 'id' => 'tr_xxx', 'status' => 'paid']);

// After — typed factories with named args
MockResponse::payment(
    id: 'tr_xxx',
    status: PaymentStatus::Paid,
    amount: new Money('EUR', '10.00'),
);
```

Factories produce valid response structures — consumers specify only what they care about, sensible defaults fill the rest.

### Lazy pagination

```php
foreach ($client->payments->iterator() as $payment) {
    // Automatically pages through all results
    // Next page fetched when current page is exhausted
    // Memory-efficient — one page in memory at a time
}
```

Builds on the existing `LazyCollection` class using PHP generators.

## 8. Rector Migration Ruleset

A separate Composer package: `mollie/mollie-api-php-rector`.

| Rule | What it does |
|---|---|
| `ConstantToEnumCaseRector` | `PaymentStatus::PAID` → `PaymentStatus::Paid` across all 37 type classes |
| `StringComparisonToEnumRector` | `$status === 'paid'` → `$status === PaymentStatus::Paid` where type context is available |
| `StdClassAccessToTypedPropertyRector` | Adds type hints to surrounding code where `\stdClass` access patterns are detected |
| `FactoryArrayToNamedArgsRector` | Converts factory array calls to named-argument constructor calls where applicable |

Shipped as a separate package — consumers install it temporarily to migrate, then remove it. Keeps the main SDK dependency-free of Rector.

### Internal code generation

A separate generator script (under `tools/` or `scripts/`) produces the `HasCurrencyConvenienceMethods` trait from a currency list. This is not part of the consumer-facing Rector package. CI validates the committed trait matches the generator output.

## 9. Release Checklist

### Before development

- [ ] Cut `3.x` branch from current `master`
- [ ] Update `composer.json`: `require.php` → `^8.2`
- [ ] Bump dev dependencies: Pest v3, PHPStan ^2, drop Paratest
- [ ] Add `declare(strict_types=1)` to all files

### Development phases

1. Infrastructure: strict types, readonly classes, enums, constructor promotion
2. Request layer: promotion, union types, named arguments
3. Response layer: typed resources, two-tier hydration, generic `send()`
4. Exceptions: constructor promotion, `Retry-After`, all validation errors
5. New features: `fromEnv()`, retry middleware, typed mocks, lazy pagination
6. Macroable: lightweight trait on Money + value objects
7. Currency generator: script to produce `HasCurrencyConvenienceMethods`
8. Rector migration: separate package with v3 → v4 rules
9. Documentation: `UPGRADING.md`, updated README, cookbook examples

### CI pipeline

- PHP 8.2, 8.3, 8.4 matrix
- Pest v3 with `--parallel`
- PHPStan level 6
- Currency trait generator check (committed output matches generator)

### UPGRADING.md structure (by impact)

| Impact | Change | Migration |
|---|---|---|
| High | Type constants → enum cases | Rector rule automates |
| High | Resource properties typed (no more `\stdClass`) | Same property names — `->value`, `->currency` still work on `Money` |
| Medium | `send()` return type inferred via generics | Remove `@var` annotations and manual casts |
| Medium | Constructor signatures changed (promotion) | Named arguments work identically — positional callers may need reordering |
| Low | `declare(strict_types=1)` everywhere | Only affects consumers extending SDK classes |
| Low | PHPUnit → Pest | Only affects consumers running SDK tests (rare) |

### GitHub issues resolved

- mollie/mollie-api-php#875 — Generic return types on `send()`
- mollie/mollie-api-php#876 — `Money` convenience factories (`fromCents`, `fromFloat`, currency macros)

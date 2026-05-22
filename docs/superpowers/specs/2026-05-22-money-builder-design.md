# Money builder — design spec

**Date:** 2026-05-22
**Status:** Approved
**Target version:** 4.0.0
**Closes:** part of issue #876 (Money convenience factories)

## Overview

Add a fluent builder entry point to `Mollie\Api\Http\Data\Money` so consumers can construct Money instances via `Money::of('EUR')->minorUnits(1000)` instead of (or alongside) the existing static factories. The builder is consistency-driven — it matches the v4 builder idiom shipped with `PaymentIncludes`, `PaymentEmbeds`, etc. — and it is strictly additive. Existing construction paths (`new Money(...)`, `Money::fromMinorUnits(...)`, `Money::fromArray(...)`) remain valid in v4.

## Goals

- Provide a fluent construction surface that reads naturally in a Laravel/Symfony codebase: `Money::of('EUR')->minorUnits(1000)`.
- Keep the construction surface narrow — exactly 2 amount-shape methods (`minorUnits` for int, `fromString` for raw decimal string). Anything else is consumer-defined via the `Macroable` trait on `Money`.
- Preserve full BC: existing `Money` constructors and factories work unchanged.
- Match the v4 builder idiom (consistency with `PaymentIncludes::mandates()->customer()` style).

## Non-goals

- Not a replacement for `Money::fromMinorUnits()` — that static factory stays.
- Not a builder for arbitrary unit conversion (major units, cents alias, brick/money interop, moneyphp/money interop). Those are consumer extensions via macros.
- Not mutable. Both `Money` and the builder are `readonly class`.
- No new third-party dependencies. `Macroable` is the SDK's own trait.

## Public API

```php
// Two terminal methods. Both return Money directly. No ->build() or ->make() needed.
Money::of('EUR')->minorUnits(1000);        // → Money { currency: "EUR", value: "10.00" }
Money::of('EUR')->fromString('10.00');     // → Money { currency: "EUR", value: "10.00" }

// Coexists with the existing static factory.
Money::fromMinorUnits('EUR', 1000);        // ← still works
new Money('EUR', '10.00');                 // ← still works (readonly ctor)

// Currency is uppercased by Money::of() to match Mollie's API convention.
Money::of('jpy')->minorUnits(1000);        // → Money { currency: "JPY", value: "1000" }

// Negative amounts (refunds) supported on minorUnits, mirrored from fromMinorUnits.
Money::of('EUR')->minorUnits(-1000);       // → Money { currency: "EUR", value: "-10.00" }
```

The builder is intentionally one-shot: `of()` accepts a currency, and either `minorUnits()` or `fromString()` terminates the chain. There is no `->in()` or `->currency()` re-setter — if a consumer needs a different currency, they call `Money::of()` again.

## Implementation

### `src/Http/Data/Money.php` — add `of()` static method

```php
readonly class Money implements Arrayable
{
    use ComposableFromArray;
    use HasCurrencyConvenienceMethods;
    use Macroable;

    public function __construct(
        public string $currency,
        public string $value,
    ) {}

    public static function of(string $currency): MoneyBuilder
    {
        return new MoneyBuilder(strtoupper($currency));
    }

    public static function fromMinorUnits(string $currency, int $amount): self
    {
        // existing implementation unchanged
    }

    public function toArray(): array
    {
        // unchanged
    }
}
```

### `src/Http/Data/MoneyBuilder.php` — new file

```php
<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Data;

readonly class MoneyBuilder
{
    public function __construct(
        private string $currency,
    ) {}

    public function minorUnits(int $amount): Money
    {
        return Money::fromMinorUnits($this->currency, $amount);
    }

    public function fromString(string $value): Money
    {
        return new Money($this->currency, $value);
    }
}
```

Two terminal methods. `minorUnits` delegates to the existing `Money::fromMinorUnits` so the currency-exponent math lives in one place. `fromString` delegates to the constructor directly.

### Why `MoneyBuilder` is its own class

- A separate class is `readonly` and small enough to be inlined mentally by readers.
- Static analysis (PHPStan) gets a precise return type at each step: `Money::of()` → `MoneyBuilder`, `->minorUnits()` → `Money`. No magic-method gymnastics.
- IDE autocomplete works for free — no `@method static` annotations needed.
- An anonymous-class or trait-based alternative would hide the type at static analysis time and tax discoverability.

## Strict typing

- `Money::of(string $currency): MoneyBuilder` — currency is required, must be a string.
- `MoneyBuilder::minorUnits(int $amount): Money` — `int` only. Passing a string fails fast.
- `MoneyBuilder::fromString(string $value): Money` — `string` only. Passing an int fails fast.

No `int|string` union types. The split is the whole point: each method's parameter type names the shape of the input.

## `Macroable` extension

`Macroable` stays on `Money`, not on `MoneyBuilder`. Consumer-defined macros operate on the final value object:

```php
Money::macro('fromBrick', fn ($brick) =>
    Money::of($brick->getCurrency()->getCurrencyCode())
        ->fromString((string) $brick->getAmount())
);

Money::fromBrick($brickMoney);    // consumer-defined factory
```

Macros on the builder itself were considered and rejected — building on the builder would require maintaining two macro registries, and consumers would inevitably split their helpers across both. One registry on the value object covers every extension case.

## Tests

New file: `tests/Http/Data/MoneyBuilderTest.php`. Existing `Money` tests stay unchanged.

Test cases (Pest v3 syntax, parallel-safe):

```
of('EUR')->minorUnits(1000)         → Money { currency: "EUR", value: "10.00" }
of('jpy')->minorUnits(1000)         → Money { currency: "JPY", value: "1000" }     // uppercase + no-decimal currency
of('BHD')->minorUnits(1000)         → Money { currency: "BHD", value: "1.000" }     // 3-decimal currency
of('EUR')->minorUnits(-1000)        → Money { currency: "EUR", value: "-10.00" }    // negative (refund)
of('EUR')->minorUnits(0)            → Money { currency: "EUR", value: "0.00" }      // zero
of('EUR')->fromString('10.00')      → Money { currency: "EUR", value: "10.00" }     // string passthrough
of('eur')->fromString('10.00')      → Money { currency: "EUR", value: "10.00" }     // uppercase via of()
```

Plus one test asserting the builder is itself a `readonly` class (introspection).

## Docs

- `README.md` — replace the canonical Money construction snippet with `Money::of('EUR')->minorUnits(1000)`. Keep `Money::fromMinorUnits()` referenced as still-valid.
- `CHANGELOG.md` `[4.0.0]` `### Added` — one bullet.
- `UPGRADING.md` — short subsection under "Convenience constructors" noting both forms are valid.

## File touch list

| File | Change |
|---|---|
| `src/Http/Data/Money.php` | Add `of()` static method |
| `src/Http/Data/MoneyBuilder.php` | New file (~25 LOC) |
| `tests/Http/Data/MoneyBuilderTest.php` | New file (~80 LOC, 7 tests) |
| `README.md` | Update construction snippet |
| `CHANGELOG.md` | `[4.0.0]` `### Added` bullet |
| `UPGRADING.md` | Subsection on Money construction options |

Estimated total: ~140 LOC across 6 files. 2-3 hours including tests and docs.

## North star alignment

- **Principle 1 (Mirror Mollie API):** `value` and `currency` field names match Mollie's wire format. `minorUnits` semantics match Mollie's currency-exponent expectations.
- **Principle 2 (Type safety + IDE discoverability):** Builder produces precise step-wise types. Method names describe input shape. No union types or magic.
- **Principle 4 (BC sacred):** Strictly additive. Every existing construction path keeps working.
- **Principle 7 (Defer features over breaking adopters):** Macroable extension point covers the brick/money + moneyphp/money interop ask in #876 without forcing those deps on every consumer.

## Out of scope (deferred or rejected)

- `majorUnits(int $amount)` method — not requested in this round; can be added in 4.1 if real demand surfaces.
- Brick/money + moneyphp/money interop methods (#876 wish list) — consumer-defined via Macroable. Documented in UPGRADING.md as the recommended pattern.
- Float input support — explicitly rejected by `fromMinorUnits` for the same precision-loss reason. Builder follows that precedent.
- Builder for other value objects (`Address::of()`, `Url::of()`) — not part of this design. Money is the high-traffic case where the builder pays off; the others can adopt the pattern later if useful.

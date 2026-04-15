# Custom Money factory via Macroable

How to add your own factory methods to `Money` without subclassing.

## Why

`Money` is a `readonly class` in v4, which means you can no longer subclass it with a non-readonly child. Instead, register a **macro** — a closure invoked as a static (or instance) method on `Money`.

This is how loyalty points, internal credits, or domain-specific shortcuts can live next to the SDK without inheritance.

## The Code

```php
use Mollie\Api\Http\Data\Money;

// Register once during application bootstrap
Money::macro('loyaltyPoints', static function (int $points): Money {
    // 100 points = €1.00
    return Money::fromMinorUnits('EUR', $points);
});

Money::macro('xau', static function (string $value): Money {
    return new Money(currency: 'XAU', value: $value);
});

// Use anywhere
$amount = Money::loyaltyPoints(2500);    // €25.00
$gold   = Money::xau('1.00000');          // 1 troy ounce
```

## Constraints

- `Money` is `readonly` — macros must **return new instances**, never mutate state.
- Calling an undefined static or instance method on `Money` throws `BadMethodCallException` with a clear message (instead of PHP's default fatal error). Catch `Exception`, not `Error`.
- Use `Money::hasMacro('loyaltyPoints')` to check before registering twice in tests.
- `Money::flushMacros()` clears every registered macro for the class — useful in `tearDown`.

## Why not subclass?

In v3 you could `class TaxedMoney extends Money { public string $tax; }`. In v4 the parent is `readonly class`, so the child must also be declared `readonly`, and constructor promotion means the child has to repeat all parent fields. Macros are simpler for the common case of "just add another factory."

If you need genuinely new state (extra properties), declare your own `readonly final class` rather than extending `Money`.

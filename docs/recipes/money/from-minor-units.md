# Money from minor units

How to construct a `Money` value object from an integer amount in the currency's minor unit (e.g. cents for EUR, yen for JPY, fils for BHD).

## Why

Most order/billing systems store amounts as integers in the minor unit to avoid floating-point rounding bugs. `Money::fromMinorUnits()` does the conversion for you and respects each currency's exponent (2 for EUR/USD, 0 for JPY, 3 for BHD).

This resolves [issue #876](https://github.com/mollie/mollie-api-php/issues/876).

## The Code

```php
use Mollie\Api\Http\Data\Money;

$eur = Money::fromMinorUnits('EUR', 1000);   // value: "10.00", currency: "EUR"
$jpy = Money::fromMinorUnits('JPY', 1000);   // value: "1000",  currency: "JPY"  (zero-decimal)
$bhd = Money::fromMinorUnits('BHD', 1000);   // value: "1.000", currency: "BHD"  (three-decimal)

// Negative amounts (refunds) are allowed
$refund = Money::fromMinorUnits('EUR', -250); // value: "-2.50"

// Currency code is normalised to upper case
$eur = Money::fromMinorUnits('eur', 1000);   // currency: "EUR"
```

## Use it in a request

```php
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\CreatePaymentRequest;

$payment = $mollie->send(new CreatePaymentRequest(
    description: "Order #{$orderId}",
    amount: Money::fromMinorUnits('EUR', $order->totalCents),
    redirectUrl: 'https://example.org/return',
));
```

## Notes

- The input is **always an integer** — float input is intentionally rejected. Binary floats can't represent decimal money exactly. If you have a float, format it to a string yourself.
- Unknown currency codes fall back to a 2-digit exponent. Add custom currencies via the [Macroable extension point](custom-factory.md) if you need different behavior.

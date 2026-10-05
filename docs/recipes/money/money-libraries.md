# Using money libraries with macros

Convert money objects from your application's money library to the SDK's `Money` value object with [custom factory macros](custom-factory.md). Register the adapter once during application bootstrap, after loading Composer's autoloader. These methods are application-defined macros, not built-in SDK methods.

This recipe follows [Jenthe Noordsij's suggestion in #940](https://github.com/mollie/mollie-api-php/pull/940#issuecomment-5951559307) to document the adapters. The SDK does not require either library. Install only the library your application uses and copy its adapter; both adapters can coexist if you use both libraries.

When combining the examples in one bootstrap file, include the shared SDK `Money` import only once. Pass the resulting `$amount` to a request, as in the [create payment recipe](../payments/create-payment.md).

## MoneyPHP

Install [moneyphp/money](https://github.com/moneyphp/money) in your application:

```bash
composer require 'moneyphp/money:^4.9'
```

MoneyPHP stores amounts in minor units. Its [DecimalMoneyFormatter](https://www.moneyphp.org/en/stable/features/formatting.html#decimal-formatter) produces a decimal string using ISO currency metadata, without locale-specific separators or floating-point conversion.

```php
use Mollie\Api\Http\Data\Money;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money as MoneyPHP;

$formatter = new DecimalMoneyFormatter(new ISOCurrencies());

Money::macro('fromMoneyPHP', static function (MoneyPHP $money) use ($formatter): Money {
    return new Money(
        currency: $money->getCurrency()->getCode(),
        value: $formatter->format($money),
    );
});

$amount = Money::fromMoneyPHP(new MoneyPHP('12345', new Currency('EUR')));
$amount->toArray(); // ['currency' => 'EUR', 'value' => '123.45']

$large = Money::fromMoneyPHP(new MoneyPHP('123456789012345678901', new Currency('EUR')));
$large->value; // '1234567890123456789.01'
```

The macro does not round. Pass an integer minor-unit string to MoneyPHP; fractional minor units are rejected by its constructor. When calculations require rounding, choose the [MoneyPHP rounding mode](https://www.moneyphp.org/en/stable/features/operation.html#rounding-modes) in your application before conversion. For example, if your policy rounds half up:

```php
$total = new MoneyPHP('1001', new Currency('EUR'));
$share = $total->divide('2', MoneyPHP::ROUND_HALF_UP);
$amount = Money::fromMoneyPHP($share);
$amount->value; // '5.01'
```

## Brick Money

Install [brick/money](https://github.com/brick/money) in your application:

```bash
composer require 'brick/money:^0.15'
```

Brick Money's `getAmount()` returns a decimal object. Recreate it with the ISO currency code and default context to normalize the scale, including inputs using custom or automatic contexts. `RoundingMode::Unnecessary` rejects any value that would lose precision. See the upstream [creation and context APIs](https://github.com/brick/money#creating-a-money).

```php
use Brick\Math\RoundingMode;
use Brick\Money\Money as BrickMoney;
use Mollie\Api\Http\Data\Money;

Money::macro('fromBrickMoney', static function (BrickMoney $money): Money {
    $currency = $money->getCurrency()->getCurrencyCode();
    $normalized = BrickMoney::of(
        amount: $money->getAmount(),
        currency: $currency,
        roundingMode: RoundingMode::Unnecessary,
    );

    return new Money(
        currency: $currency,
        value: (string) $normalized->getAmount(),
    );
});

$amount = Money::fromBrickMoney(BrickMoney::ofMinor('12345', 'EUR'));
$amount->toArray(); // ['currency' => 'EUR', 'value' => '123.45']

$large = Money::fromBrickMoney(BrickMoney::ofMinor('123456789012345678901', 'EUR'));
$large->value; // '1234567890123456789.01'
```

For example, an automatic-context amount of `10.0` EUR becomes `10.00`; a custom-context amount of `10.005` EUR throws `Brick\Math\Exception\RoundingNecessaryException`. If your application intentionally rounds half up, do so before calling the macro:

```php
$rounded = BrickMoney::of('10.005', 'EUR', roundingMode: RoundingMode::HalfUp);
$amount = Money::fromBrickMoney($rounded);
$amount->value; // '10.01'
```

## Precision and currencies

Both adapters preserve the following minor-unit strings exactly:

| Currency | Minor-unit string | SDK `value` |
| --- | --- | --- |
| JPY (zero decimals) | `'12345'` | `'12345'` |
| EUR (two decimals) | `'12345'` | `'123.45'` |
| BHD (three decimals, formatting only) | `'12345'` | `'12.345'` |
| EUR | `'123456789012345678901'` | `'1234567890123456789.01'` |
| JPY | `'0'` | `'0'` |
| EUR | `'0'` | `'0.00'` |
| BHD (formatting only) | `'0'` | `'0.000'` |
| EUR | `'-12345'` | `'-123.45'` |

The BHD rows demonstrate three-decimal ISO formatting only. BHD is not listed in [Mollie's supported payment currencies](https://docs.mollie.com/docs/multicurrency); do not send these examples as payments. A currency recognized by either library or accepted by the SDK value object's constructor is not necessarily supported by Mollie. Check the currency, decimal places and payment method in that guide before creating requests. Payment-method rules can also affect denominations, such as PayPal's handling of HUF and TWD.

Large, zero and negative values in this recipe demonstrate local conversion, not valid payment amounts. The [payment amount limits](https://docs.mollie.com/reference/create-payment) and endpoint-specific validation still apply.

## Constraints

- Keep amounts as strings or library decimal objects throughout. Do not cast to `float` or `int`, divide with PHP's `/`, or use locale-aware display formatters. The large examples exceed PHP's integer range.
- MoneyPHP uses `ISOCurrencies`; unknown currency codes fail during formatting. Brick's adapter resolves the code through its ISO currency provider; unknown or custom currency codes fail during normalization. Neither adapter converts between currencies.
- Both macros return new SDK instances. They do not mutate the source objects or apply an implicit rounding policy.
- Macro registrations are process-local. Register them in each application or worker bootstrap where needed. Copy only the adapter whose optional dependency is installed. See [custom factory constraints](custom-factory.md#constraints) for checking and clearing registrations.

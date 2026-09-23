# List Payment Methods

How to retrieve all available payment methods with the Mollie API.

## The Code

```php
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Requests\GetAllMethodsRequest;

try {
    $methods = $mollie->send(
        new GetAllMethodsRequest(
            includeIssuers: false,
            includePricing: false,
            locale: 'nl_NL',
            amount: new Money(currency: 'EUR', value: '100.00')
        )
    );

    foreach ($methods as $method) {
        // Method::$image is ?stdClass — guard it before reading the sizes.
        $image = $method->image;

        echo '<div style="line-height:40px; vertical-align:top">';

        if ($image !== null) {
            echo '<img src="' . htmlspecialchars($image->size1x) . '" srcset="' . htmlspecialchars($image->size2x) . ' 2x"> ';
        }

        echo htmlspecialchars($method->description) . ' (' . htmlspecialchars($method->id) . ')';
        echo '</div>';
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## The Response

```php
$method->id;            // "ideal"
$method->description;   // "iDEAL"
$method->image;         // stdClass with size1x/size2x/svg, or null
$method->minimumAmount; // Mollie\Api\Http\Data\Money, or null
$method->maximumAmount; // Mollie\Api\Http\Data\Money, or null
```

`image`, `minimumAmount` and `maximumAmount` are all nullable, so read them
through a local variable rather than chaining off the property:

```php
$minimum = $method->minimumAmount;
$maximum = $method->maximumAmount;

echo 'Minimum: '.($minimum === null ? 'no minimum' : "{$minimum->currency} {$minimum->value}")."\n";
echo 'Maximum: '.($maximum === null ? 'no maximum' : "{$maximum->currency} {$maximum->value}")."\n";
```

## Additional Notes

- Use `sequenceType` to filter methods available for recurring payments
- The `locale` parameter affects translations of method names and descriptions
- The `amount` parameter filters methods available for that specific amount
- The `billingCountry` parameter filters methods available in that country
- Most methods include image URLs for regular (1x) and retina (2x) displays, but `image` can be `null`
- Methods may have minimum and maximum amount constraints; both are `null` when unconstrained

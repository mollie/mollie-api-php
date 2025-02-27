# List Payment Methods

How to retrieve all available payment methods with the Mollie API.

## The Code

```php
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
        echo '<div style="line-height:40px; vertical-align:top">';
        echo '<img src="' . htmlspecialchars($method->image->size1x) . '" srcset="' . htmlspecialchars($method->image->size2x) . ' 2x"> ';
        echo htmlspecialchars($method->description) . ' (' . htmlspecialchars($method->id) . ')';
        echo '</div>';
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## The Response

```php
$method->id;          // "ideal"
$method->description; // "iDEAL"
$method->image->size1x; // "https://www.mollie.com/external/icons/payment-methods/ideal.png"
$method->image->size2x; // "https://www.mollie.com/external/icons/payment-methods/ideal%402x.png"
$method->minimumAmount->value;    // "0.01"
$method->minimumAmount->currency; // "EUR"
$method->maximumAmount->value;    // "50000.00"
$method->maximumAmount->currency; // "EUR"
```

## Additional Notes

- Use `sequenceType` to filter methods available for recurring payments
- The `locale` parameter affects translations of method names and descriptions
- The `amount` parameter filters methods available for that specific amount
- The `billingCountry` parameter filters methods available in that country
- Each method includes image URLs for regular (1x) and retina (2x) displays
- Methods may have minimum and maximum amount constraints

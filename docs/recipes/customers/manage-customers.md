# Manage Customers

How to create, update, and delete customers using the Mollie API.

## Create a Customer

```php
use Mollie\Api\Http\Requests\CreateCustomerRequest;

try {
    // Create a new customer
    $customer = $mollie->send(
        new CreateCustomerRequest(
            name: 'Luke Skywalker',
            email: 'luke@example.com',
            locale: 'en_US',
            metadata: [
                'isJedi' => true
            ]
        )
    );

    echo "New customer created: {$customer->id}\n";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## Update a Customer

```php
use Mollie\Api\Http\Requests\GetCustomerRequest;
use Mollie\Api\Http\Requests\UpdateCustomerRequest;

try {
    // First retrieve the customer you want to update
    $customer = $mollie->send(
        new GetCustomerRequest(
            id: 'cst_8wmqcHMN4U'
        )
    );

    // Update specific customer fields
    $customer = $mollie->send(
        new UpdateCustomerRequest(
            id: $customer->id,
            name: 'Luke Sky',
            email: 'luke@example.com',
            locale: 'en_US',
            metadata: [
                'isJedi' => true
            ]
            // Fields we don't specify will keep their current values
        )
    );

    echo "Customer updated: {$customer->name}\n";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## Delete a Customer

```php
use Mollie\Api\Http\Requests\DeleteCustomerRequest;

try {
    // Delete a customer
    $mollie->send(
        new DeleteCustomerRequest(
            id: 'cst_8wmqcHMN4U'
        )
    );

    echo "Customer deleted\n";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## The Response

```php
$customer->id;          // "cst_8wmqcHMN4U"
$customer->name;        // "Luke Skywalker"
$customer->email;       // "luke@example.com"
$customer->locale;      // "en_US"
$customer->metadata;    // Object containing custom metadata
$customer->mode;        // "live" or "test"
$customer->createdAt;   // "2024-02-24T12:13:14+00:00"
```

## Additional Notes

- Customers are used to store recurring payment details
- Each customer can have multiple mandates for different payment methods
- The customer ID is required for:
  - Creating recurring payments
  - Creating subscriptions
  - Retrieving payment history
- Customer data should be kept in sync with your own database
- Available locales:
  - `en_US` English (US)
  - `en_GB` English (UK)
  - `nl_NL` Dutch (Netherlands)
  - `nl_BE` Dutch (Belgium)
  - `fr_FR` French (France)
  - `fr_BE` French (Belgium)
  - `de_DE` German (Germany)
  - `de_AT` German (Austria)
  - `de_CH` German (Switzerland)
  - `es_ES` Spanish (Spain)
  - `ca_ES` Catalan (Spain)
  - `pt_PT` Portuguese (Portugal)
  - `it_IT` Italian (Italy)
  - `nb_NO` Norwegian (Norway)
  - `sv_SE` Swedish (Sweden)
  - `fi_FI` Finnish (Finland)
  - `da_DK` Danish (Denmark)
  - `is_IS` Icelandic (Iceland)
  - `hu_HU` Hungarian (Hungary)
  - `pl_PL` Polish (Poland)
  - `lv_LV` Latvian (Latvia)
  - `lt_LT` Lithuanian (Lithuania)

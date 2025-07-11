# Manage Customer Mandates

How to create, list, and revoke mandates for recurring payments using the Mollie API.

## Create a Mandate

```php
use Mollie\Api\Http\Requests\CreateMandateRequest;
use Mollie\Api\Types\MandateMethod;

try {
    // Create a SEPA Direct Debit mandate
    $mandate = $mollie->send(
        new CreateMandateRequest(
            customerId: 'cst_8wmqcHMN4U',
            method: MandateMethod::DIRECTDEBIT,
            consumerName: 'B. A. Example',
            consumerAccount: 'NL34ABNA0243341423'
        )
    );

    echo "New mandate created: {$mandate->id}\n";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## List Mandates

```php
use Mollie\Api\Http\Requests\GetPaginatedMandateRequest;

try {
    // List all mandates for a customer
    $response = $mollie->send(
        new GetPaginatedMandateRequest(
            customerId: 'cst_8wmqcHMN4U'
        )
    );

    foreach ($response as $mandate) {
        echo "Mandate {$mandate->id}:\n";
        echo "- Method: {$mandate->method}\n";
        echo "- Status: {$mandate->status}\n";
        echo "- Details: {$mandate->details->consumerName}\n";
        echo "          {$mandate->details->consumerAccount}\n\n";
    }
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## Revoke a Mandate

```php
use Mollie\Api\Http\Requests\RevokeMandateRequest;

try {
    // Revoke a specific mandate
    $mollie->send(
        new RevokeMandateRequest(
            customerId: 'cst_8wmqcHMN4U',
            mandateId: 'mdt_h3gAaD5zP'
        )
    );

    echo "Mandate revoked\n";
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

## The Response

```php
$mandate->id;                // "mdt_h3gAaD5zP"
$mandate->status;           // "valid", "pending", "invalid"
$mandate->method;           // "directdebit"
$mandate->details;          // Object containing mandate details
$mandate->customerId;       // "cst_8wmqcHMN4U"
$mandate->createdAt;        // "2024-02-24T12:13:14+00:00"
$mandate->signatureDate;    // "2024-02-24" (optional)
$mandate->mandateReference; // "YOUR-COMPANY-MD13804" (optional)
```

## Additional Notes

- Mandates are used for recurring payments
- A customer can have multiple mandates
- Only valid mandates can be used for payments
- Available mandate methods:
  - `directdebit`: SEPA Direct Debit
  - `creditcard`: Credit card
- Mandate status:
  - `valid`: The mandate is valid and can be used for payments
  - `pending`: The mandate is pending and cannot be used yet
  - `invalid`: The mandate is invalid and cannot be used

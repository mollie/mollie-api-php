# Upgrading from v2 to v3

Welcome to Mollie API PHP v3! This guide will help you smoothly transition from v2 to v3.

The codebase has significant improvements, focusing on modern PHP practices, enhanced type safety, and offers a more intuitive developer experience.

## Breaking Changes

### Deprecations

#### MethodEndpointCollection.allActive()

The method `MethodEndpointCollection.allActive()` has been removed. Use `MethodEndpointCollection.allEnabled()` instead.

#### Order endpoint

Orders: Mollie is deprecating the Order and Shipment endpoints so these have been removed from `mollie-api-php`. The same functionality is now available through the Payment endpoint as well. So, use the Payment endpoint instead.

  - All `/orders/*` endpoints and related classes (`Order*Endpoint`)
  - Removed `MollieApiClient` properties:
    ```php
    $client->orderPayments;  // Removed
    $client->orderRefunds;   // Removed
    $client->orderLines;     // Removed
    $client->shipments;      // Removed
    ```

#### Integration code examples

To prevent misuse the code samples in `/examples` were replaced by markdown "recipes", which can be found in `/docs/recipes`.

### Metadata Type Restriction

In v2, when making API requests, the metadata parameter accepted any type (string, array, object, etc.). In v3, metadata in request payloads is restricted to only accept arrays. Make sure to update your code to provide metadata as arrays when making API requests.

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
You can now use named parameters for improved readability and flexibility:

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

- **Streamlined constants** - Redundant prefixes have been removed for a cleaner API:
  ```php
  // Before
  Payment::STATUS_PAID;

  // After
  Payment::PAID;
  ```

- **Simplified SequenceType constants**:
  ```php
  // Before
  SequenceType::SEQUENCETYPE_FIRST;

  // After
  SequenceType::FIRST;
  ```

- **Cleaner collection initialization**:
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
You can now use any PSR-18 compatible HTTP client:

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
You can now say goodbye to array-based payloads and hello to type-safe request objects:

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

Collections now feature powerful functional methods for more expressive code:

```php
// New methods
$activePayments = $payments->filter(fn($p) => $p->isActive());
$hasRefunds = $payments->contains(fn($p) => $p->hasRefunds());
```

### Method Issuer Contracts

You can now easily manage method issuers with optional contract IDs:

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

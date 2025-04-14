# Upgrading from v2 to v3

Welcome to Mollie API PHP v3! This guide will help you smoothly transition from v2 to v3.

The codebase has significant improvements, focusing on modern PHP practices, enhanced type safety, and offers a more intuitive developer experience.

## Breaking Changes

### Removed Endpoints & Classes

Mollie is deprecating the Order and Shipment endpoints so v3 is discontinuing support for these. Use the recently enhanced Payment endpoint instead.

> Note: Support for Klarna, Billie, in3 and vouchers will be added to Mollie's Payment API later - consider delaying to upgrade this package if you rely on these specific payment methods.
> 
> Keep an eye on the [Mollie Changelog](https://docs.mollie.com/changelog/) to stay informed. 

**Removed:**

- All `/orders/*` endpoints and related classes (`Order*Endpoint`)
- Removed `MollieApiClient` properties:
  ```php
  $client->orderPayments;  // Removed
  $client->orderRefunds;   // Removed
  $client->orderLines;     // Removed
  $client->shipments;      // Removed
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

## Deprecations

- `MethodEndpointCollection@allActive()` - Use `allEnabled()`
- Array-style payloads (migrate to typed requests)
- To prevent misuse the code samples in `/examples` were replaced by markdown based recipes in `/docs/recipes` 

## Further reading

Usage guides for the PHP client can be found in the [docs](docs). For more information on the Mollie API, check out [the official Mollie docs](https://docs.mollie.com).

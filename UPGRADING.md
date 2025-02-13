# Upgrading from v2 to v3

## Breaking Changes

### 1. Removed Endpoints & Classes
- **Full order system removal**:
  - All `/orders/*` endpoints and related classes (`Order*Endpoint`)
  - Removed `MollieApiClient` properties:
    ```php
    $client->orderPayments;   // Removed
    $client->orderRefunds;   // Removed
    $client->orderLines;     // Removed
    $client->shipments;      // Removed
    ```

### 2. Method & Class Renames
#### Endpoint Class Changes
| Old Class                          | New Class                               | Method Changes                           |
|------------------------------------|-----------------------------------------|------------------------------------------|
| `MethodEndpoint`                   | `MethodEndpointCollection`             | `allAvailable()` → `all()`<br>`all()` → `allEnabled()` |
| `BalanceTransactionEndpoint`       | `BalanceTransactionEndpointCollection` | `listFor()` → `pageFor()`<br>`listForId()` → `pageForId()` |
| `CustomerPaymentsEndpoint`         | `CustomerPaymentsEndpointCollection`   | `listFor()` → `pageFor()`<br>`listForId()` → `pageForId()` |
| `MandateEndpoint`                  | `MandateEndpointCollection`            | `listFor()` → `pageFor()`<br>`listForId()` → `pageForId()` |
| `PaymentRefundEndpoint`            | `PaymentRefundEndpointCollection`      | `listFor()` → `pageFor()`<br>`listForId()` → `pageForId()` |
| `OnboardingEndpoint`               | `OnboardingEndpointCollection`         | `get()` → `status()`                     |
| `SubscriptionEndpoint`             | `SubscriptionEndpointCollection`       | `page()` → `allFor()`                    |

#### Signature Changes
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

### 3. Constant & Collection Changes
- **Removed `STATUS_` prefix**:
  ```php
  // Before
  Payment::STATUS_PAID;

  // After
  Payment::PAID;
  ```

- **Removed `SEQUENCETYPE_` prefix** from SequenceType constants:
  ```php
  // Before
  SequenceType::SEQUENCETYPE_FIRST;

  // After
  SequenceType::FIRST;
  ```

- **Collection constructor changes**:
  ```php
  // Before
  new PaymentCollection(10, $payments);

  // After
  new PaymentCollection($payments);
  ```

## New Features

### 1. Modern HTTP Handling
#### PSR-18 Support
```php
use Mollie\Api\HttpAdapter\PSR18MollieHttpAdapter;

$adapter = new PSR18MollieHttpAdapter(
    new GuzzleClient(),
    new Psr17Factory(),
    new Psr17Factory()
);

$client = new MollieApiClient($adapter);
```

### 2. Enhanced Request Handling
#### Typed Request Objects
```php
use Mollie\Api\Http\Requests\CreatePaymentRequest;

$request = new CreatePaymentRequest(
    amount: new Money('EUR', '10.00'),
    description: 'Order 123',
    redirectUrl: 'https://example.com/redirect'
);

$payment = $client->send($request);
```

### 3. Collection Improvements
```php
// New methods
$activePayments = $payments->filter(fn($p) => $p->isActive());
$hasRefunds = $payments->contains(fn($p) => $p->hasRefunds());
```

## Configuration Changes

### 1. Test Mode Handling
- **Automatic detection** with API keys
- **Explicit parameter** for organization credentials:
  ```php
  // Get payment link in test mode
  $link = $client->paymentLinks->get('pl_123', testmode: true);
  ```

### 2. Method Issuer Contracts
```php
$client->methodIssuers->enable(
    profileId: 'pfl_123',
    methodId: 'voucher',
    issuerId: 'iss_456',
    contractId: 'contract_789'  // Optional
);
```

## Deprecations

### 1. Method Deprecations
- `MethodEndpointCollection@allActive()` - Use `allEnabled()`
- Array-style payloads (migrate to typed requests)

### 2. Removed Collections
- `OrganizationCollection`
- `RouteCollection`

## Migration Checklist
1. Replace all `listFor()`/`page()` calls with new method names
2. Update status constant references (remove `STATUS_` prefix)
3. Migrate collection instantiation to new constructor format
4. Add `testmode` parameters where using organization credentials
5. Convert array payloads to typed request objects (recommended)

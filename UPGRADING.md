# Upgrading

## From v2 to v3

### High Impact Changes

#### Removed Endpoints

All `/orders` endpoint have been removed. This removal effects all `Order*Endpoint` classes. The following properties were removed from the `MollieApiClient`:

- `orderPayments`
- `orderRefunds`
- `orderLines`
- `orderPayments`

This removal also effects the `/orders/{orderId}/shipments` endpoints and the corresponding `Shipment*Endpoint` classes. The following properties were removed: `shipments`.

### Removed unused Collections
This change should not have any impact on your code, but if you have a type hint for any of the following classes, make sure to remove it
- `Mollie\Api\Resources\OrganizationCollection`
- `Mollie\Api\Resources\RouteCollection`

### Removed deprecations
The following has been deprecated
- `Mollie\Api\Types\OrderStatus::REFUNDED`
- `Mollie\Api\Types\OrderLineStatus::REFUNDED`
- all Orders related endpoints
  - properties starting with `orders` prefix or related to any `Order*Endpoint`
    - `orderPayments`
    - `orderRefunds`
    - `orderLines`
    - `orderPayments`
  - Shipments endpoint (all properties prefixed with `shipments` / `Shipment*Endpoint`)

### Removed non-valid method params
**Mollie\Api\EndpointCollection\CustomerPaymentsEndpointCollection**
- `createFor()` and `createForId()` removed third argument `$filters`

**Mollie\Api\EndpointCollection\InvoiceEndpointCollection**
- `get()` removed second argument `$parameters`

**Mollie\Api\EndpointCollection\MandateEndpointCollection**
- `createFor()` and `createForId()` removed third argument `$filters`

**Mollie\Api\EndpointCollection\PaymentCaptureEndpointCollection**
- `createFor()` and `createForId()` removed third argument `$filters`

### Removed methods
**Mollie\Api\EndpointCollection\InvoiceEndpointCollection**
- `all()` was removed -> use `page()` instead

### change of function names
Accross the codebase we have had inconsistent namings like `listFor()` as well as `pageFor()` and `page()`. Those have been standardized. Endpoints that return a paginated response use the `page*()` naming while non-paginated endpoints use `list*()`. The following method names were changed.

**Mollie\Api\EndpointCollection\BalanceTransactionEndpointCollection**
- `balanceTransactions->listFor()` into `balanceTransactions->pageFor()`
- `balanceTransactions->listForId()` into `balanceTransactions->pageForId()`

**Mollie\Api\EndpointCollection\CustomerPaymentsEndpointCollection**
- `customerPayments->listFor()` into `customerPayments->pageFor()`
- `customerPayments->listForId()` into `customerPayments->pageForId()`

**Mollie\Api\EndpointCollection\MandateEndpointCollection**
- `mandates->listFor()` into `mandates->pageFor()`
- `mandates->listForId()` into `mandates->pageForId()`

**Mollie\Api\EndpointCollection\PaymentRefundEndpointCollection**
- `paymentRefunds->listFor()` into `paymentRefunds->pageFor()`
- `paymentRefunds->listForId()` into `paymentRefunds->pageForId()`

**Mollie\Api\EndpointCollection\MethodEndpointCollection**
- `methods->allAvailable()` has been renamed into `methods->all()` now returns all available methods (both enabled and disabled) - previously returned only all enabled methods
- former `methods->all()` has been renamed to `methods->allEnabled()`
- `methods->allActive()` is deprecated

The reasoning behind this change is to make the method names more intuitive:
- `all()` returns ALL methods (both enabled and disabled)
- `allEnabled()` returns only the enabled methods (previously called `allActive()`)
- The `allActive()` method is deprecated and will be removed in v4

**Mollie\Api\EndpointCollection\OnboardingEndpointCollection**
- `get()` was changed into `status()`
- depricated `submit()` and `create()` were removed -> use `ClientLinkEndpointCollection@create()` instead

**Mollie\Api\EndpointCollection\PaymentCaptureEndpointCollection**
- `paymentCaptures->listFor()` into `paymentCaptures->pageFor()`
- `paymentCaptures->listForId()` into `paymentCaptures->pageForId()`

**Mollie\Api\EndpointCollection\PaymentChargebackEndpointCollection**
- `listFor()` into `pageFor()`
- `listForId()` into `pageForId()`

**Mollie\Api\EndpointCollection\PaymentRefundEndpointCollection**
- `pageFor(Payment $payment, array $parameters = [])` changed to `pageFor(Payment $payment, ?string $from = null, ?int $limit = null, array $filters = [])`
- `pageForId(string $paymentId, array $parameters = [])` changed to `pageForId(string $paymentId, ?string $from = null, ?int $limit = null, array $filters = [])`

**Mollie\Api\EndpointCollection\SubscriptionEndpointCollection**
- `listFor` changed to `pageFor`
- `listForId` changed to `pageForId`
- `page` which previously returned all subscriptions, was renamed into `allFor`
- `allForId` and `iteratorForAll` were added to return all subscriptions

### Renamed methods
**Mollie\Api\EndpointCollection\PermissionEndpointCollection**
- `all()` was renamed to `list()` to maintain consistency with other non-paginated endpoints

### Removed non-valid method params
**Mollie\Api\EndpointCollection\PermissionEndpointCollection**
- `get()` second argument changed from `array $parameters` to `array|bool $testmode` to match API documentation

# Changelog
### Type cast embeded Resources
In previous versions resources requested via `embed` param on requests like [get-payment](https://docs.mollie.com/reference/get-payment) were not casted into their respective collection or resource classes. Starting with this version all embeded resources are typecasted.

```php
$payment = $mollie->payments->get('...', ['embed' => ['refunds']]);

$this->assertInstanceOf(RefundCollection::class, $payment->_embedded->refunds);
```

### Added CaptureMode
A new `Mollie\Api\Types\CaptureMode` class which can be used when using on the [create-payment request](https://docs.mollie.com/reference/create-payment) (s. *captureMode*) when using the [capture feature](https://docs.mollie.com/reference/create-capture).

### PSR-18 Support
We added a new HTTP-adapter which supports PSR-18. The following example demonstrates on how to use the new adapter.

**Note**: The example uses `nyholm/psr7` to get all necessary factories required. You can use the same factories by running `composer require nyholm/psr7`.

```php
use Mollie\Api\MollieApiClient;
use Psr\Http\Client\ClientInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Mollie\Api\HttpAdapter\PSR18MollieHttpAdapter;

// Create instances of the required classes
$httpClient = new GuzzleClient(); // Instance of ClientInterface
$requestFactory = new Psr17Factory(); // Instance of RequestFactoryInterface
$streamFactory = new Psr17Factory(); // Instance of StreamFactoryInterface

// Instantiate the PSR18MollieHttpAdapter
$mollieHttpAdapter = new PSR18MollieHttpAdapter(
  $httpClient,
  $requestFactory,
  $streamFactory
);

$client = new MollieApiClient($mollieHttpAdapter);

$client->setApiKey("test_qM2fCcTADeP6m87E5yFbnzfcUGpEDb");
$client->payments->page();
```

### Added Collection Methods
Two new collection methods were added which can be used to simplify interacting with collection resources.

- `contains(callable $callback): bool`
- `filter(callable $callback): self`

### Testmode is automatically removed..
..If an API key is used as authentication.

Say Goodby to the annoying `[...]. Try switching live / test API keys.`

### Requests, Payloads and Queries
The new version can be used as before **without any changes to the codebase** (except the few mentioned above). The underlying codebase was changed drastically to allow a few improvements. There are now 3 different ways to interact with the client.

#### The old way
Just as you used to... Pass in arrays of data and receive your Resource/ResourceCollection.

```php
// old way of creating a payment
$payment = $mollie->payments->create([
    "amount" => [
        "currency" => "EUR",
        "value" => "10.00"
    ],
    "description" => "My first API payment",
    "redirectUrl" => "https://webshop.example.org/order/12345/",
    "webhookUrl"  => "https://webshop.example.org/mollie-webhook/",
]);
```

#### Slightly improved
With this approach you can use new Objects and therefore actually know what data is required to pass into the method, but you can still using the old way of calling the request.

```php
// improved
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Data\CreatePaymentPayload;

$payload = new CreatePaymentPayload(
    description: 'My first API payment',
    amount: new Money('EUR', '10.00'),
    redirectUrl: 'https://webshop.example.org/order/12345/',
    webhookUrl: 'https://webshop.example.org/mollie-webhook/'
);

$payment = $mollie->payments->create($payload);
```
#### This is the way!
Finally, the new way of interacting with the client:
1. create your payload
2. pass it into your request
3. send it
4. inspect response
4b. receive your Resoure/ResourceCollection

```php
// newest ;-)
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Data\CreatePaymentPayload;
use Mollie\Api\Http\Requests\CreatePaymentRequest;

$payload = new CreatePaymentPayload(
    description: 'My first API payment',
    amount: new Money('EUR', '10.00'),
    redirectUrl: 'https://webshop.example.org/order/12345/',
    webhookUrl: 'https://webshop.example.org/mollie-webhook/'
);

$response = $mollie->send($payload);

$payment = $response->toResource();
$jsonData = $response->json();
$status = $response->status();
```

With this you get a `Response` and can also inspect its status, body or any other payload. If you want to use the `$client->send()` method but don't want to call the `->toResource()` method to receive your Resource, you can simply call `MollieApiClient::shouldAutoHydrate()`.

---

## Some Context...
..on how the new request cycle works
<img width="1190" alt="Screenshot 2024-09-09 at 11 03 17" src="https://github.com/user-attachments/assets/89c8ba43-bde5-4619-82e9-7f1ef752d7de">

### Added contractId parameter for Method Issuers
The `enable()` method on the `MethodIssuerEndpointCollection` now supports an optional `contractId` parameter when enabling voucher issuers. This parameter can be used when an intermediary party (contractor) is involved([1](https://docs.mollie.com/reference/enable-method-issuer)).

```php
// Enable a method issuer with a contract ID
$issuer = $mollie->methodIssuers->enable(
    profileId: 'pfl_...',
    methodId: 'voucher',
    issuerId: 'issuer_id',
    contractId: 'contract_123' // Optional parameter
);
```

The contract ID can be updated as long as it hasn't been approved yet by repeating the API call with a different contract ID.

### Added optional testmode parameter
The following methods now accept an optional `testmode` parameter:

**Mollie\Api\EndpointCollection\PaymentLinkEndpointCollection**
- `get(string $paymentLinkId, ?bool $testmode = null)`
- `update(string $paymentLinkId, $payload = [], ?bool $testmode = null)`
- `delete(string $paymentLinkId, ?bool $testmode = null)`

This parameter can be used when working with organization-level credentials such as OAuth access tokens to specify whether the operation should be performed in test mode. For API key credentials, this parameter can be omitted as the mode is determined by the key type.

```php
// Example with testmode parameter
$paymentLink = $mollie->paymentLinks->get('pl_...', testmode: true);
$paymentLink = $mollie->paymentLinks->update('pl_...', ['description' => 'Updated'], testmode: true);
$mollie->paymentLinks->delete('pl_...', testmode: true);
```

---

- removed `STATUS` prefix on all type constants
- collection constructors changed, they don't include the counted items anymore

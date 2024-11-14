- [x] remove `OrganizationsCollection` and change parent class of `OrganizationEndpoint` to `EndpointAbstract`
- [x] remove `RouteCollection` and change parent class of `PaymentRouteEndpoint` to `EndpointAbstract`
- [ ] check naming methods and standardise them (e.g. `SettlementCaptureEndpoint@pageForId()` vs `PaymentChargbackEndpoint@listForId()`)
- [x] Type cast embedded resources. I.e. when including refunds and chargebacks on a GET payment request, the refunds and chargebacks are not type cast.
- [x] [PSR-18 Support](https://github.com/mollie/mollie-api-php/issues/703)
- [ ] ~~rename `MethodEndpoint@all()` or remove it to avoid confusion over `allAvailable()` vs `all()`~~ -> marked as deprecated
- [x] Add Type: CaptureMode
- [x] change return types on resources
- [ ] check resources that have embedded resources whether they need to call an endpoint to provide the data
- [ ] check endpoint calls in resources for unnecessary code
	- [x] check if we can add a trait for getPresetOptions and withPresetOptions
- [x] removed deprecated OrderStatus::REFUNDED OrderLineStatus::REFUNDED
- [x] refactored collections for easier usage
- [ ] update documentation
- [ ] add sessions endpoint (added by @sandervanhooft)
---
Posting this here for now. For the upgrading steps I have created a separate file. For the changelog we don't have any file yet. May be worth to start a `CHANGELOG.md` file which is automatically filled via a github action from the release notes.

# Upgrading
## From v2 to v3
### Removed unused Collections
This change should not have any impact on your code, but if you have a type hint for any of the following classes, make sure to remove it
- `Mollie\Api\Resources\OrganizationCollection`
- `Mollie\Api\Resources\RouteCollection`


### Removed deprecations
The following was removed due to a deprecation
- `Mollie\Api\Types\OrderStatus::REFUNDED`
- `Mollie\Api\Types\OrderLineStatus::REFUNDED`

### Standardisation of function names
Accross the codebase we have had inconsistent namings like `listFor()` as well as `pageFor()` and `page()`. Those have been standardized. The following method names were changed.

**Mollie\Api\Endpoints\BalanceTransactionEndpointCollection**
- `balanceTransactions->listFor()` into `balanceTransactions->page()`
- `balanceTransactions->listForId()` into `balanceTransactions->pageForId()`

**Mollie\Api\Endpoints\PaymentRefundEndpointCollection**
- `paymentRefunds->listFor()` into `paymentRefunds->pageFor()`
- `paymentRefunds->listForId()` into `paymentRefunds->pageForId()`

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
- `filter(callable $callback): static`

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
use Mollie\Api\Http\Payload\Money;
use Mollie\Api\Http\Payload\CreatePaymentPayload;

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
use Mollie\Api\Http\Payload\Money;
use Mollie\Api\Http\Payload\CreatePaymentPayload;
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

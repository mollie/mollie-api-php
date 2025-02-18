# Testing with Mollie API Client

## Test Mode Configuration

### Key Concepts
- Test mode is automatically determined by API key prefix (`test_` or `live_`)
- Explicit `testmode` parameter available for OAuth scenarios
- Configure at global client level or per individual request

### Global Configuration
```php
use Mollie\Api\MollieApiClient;

$mollie = new MollieApiClient();
$mollie->setApiKey("test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM");
$mollie->test(true); // Applies to all subsequent requests
```

### Per-Request Configuration
```php
// Payment request with test mode
$createPaymentRequest = new CreatePaymentRequest(/* ... */);
$mollie->send($createPaymentRequest->test(true));

// Endpoint collection example
$customer = $mollie->customers->get('cust_12345678', testmode: true);
```

## API Mocking

### Basic Usage
Simulate API responses without network calls:

```php
use Mollie\Api\MollieApiClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetPaymentRequest;

$client = MollieApiClient::fake([
    GetPaymentRequest::class => new MockResponse(
        body: [
            'resource' => 'payment',
            'id' => 'tr_xxxxxxxxxxxx',
            'mode' => 'test',
            'amount' => [
                'value' => '20.00',
                'currency' => 'EUR'
            ],
            'description' => 'Test',
            'status' => 'open',
            // ...
        ],
        status: 200
    )
]);

$payment = $client->send(new GetPaymentRequest('tr_xxxxxxxxxxxx'));
```

### MockResponse Options
Configure responses using:
- **Arrays**: Direct data structure
- **Strings**: JSON payloads or predefined fixture names
- **Callables**: Dynamic response generation

```php
// Array response
MockResponse::create([
    'id' => 'tr_xxxxxxxxxxxx',
    'amount' => ['value' => '20.00', 'currency' => 'EUR']
]);

// Fixture response
MockResponse::create('payment');

// Dynamic response
MockResponse::create(function (PendingRequest $request) {
    return ['amount' => $request->hasParameter('amount') ? 10 : 20];
});
```

### Working with Collections
Create paginated list responses:

```php
use Mollie\Api\Resources\PaymentCollection;

$client = MollieApiClient::fake([
    GetPaginatedPaymentsRequest::class => MockResponse::list(PaymentCollection::class)
        ->add([
            'resource' => 'payment',
            'id' => 'tr_xxxxxxxxxxxx',
            'mode' => 'test',
            'amount' => [
                'value' => '20.00',
                'currency' => 'EUR'
            ],
            'description' => 'Test',
            'status' => 'open',
            // ...
        ])
        ->create()
]);
```

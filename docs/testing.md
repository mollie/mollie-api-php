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

### Handling Embedded Resources
Simulate HAL+JSON embedded resources using the `_embedded` property:

**Key Concepts**
- Use `MockResponse::resource()` to start building a resource response
- Chain `embed()` calls to add related collections
- Maintain resource relationships with fluent interface

```php
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\RefundCollection;
use Mollie\Api\Resources\ChargebackCollection;

$client = MollieApiClient::fake([
    GetPaymentRequest::class => MockResponse::resource(Payment::class)
        ->with([  // Main resource properties
            'resource' => 'payment',
            'id' => 'tr_xxxxxxxxxxxx',
            'amount' => [
                'value' => '20.00',
                'currency' => 'EUR'
            ]
        ])
        ->embed(RefundCollection::class)  // First embedded collection
            ->add([
                'resource' => 'refund',
                'id' => 're_12345',
                'amount' => [
                    'value' => '10.00',
                    'currency' => 'EUR'
                ]
            ])
        ->embed(ChargebackCollection::class)  // Second embedded collection
            ->add([
                'resource' => 'chargeback',
                'id' => 'chb_12345',
                'amount' => [
                    'value' => '20.00',
                    'currency' => 'EUR'
                ]
            ])
        ->create()
]);

// Resulting response will contain:
// - Payment details in main body
// - Refunds in _embedded.refunds
// - Chargebacks in _embedded.chargebacks
```

### Handling Error Responses
Simulate API error responses using dedicated helper methods or the generic error builder:

**Common Error Shortcuts**
```php
use Mollie\Api\Fake\MockResponse;

// 404 Not Found
$client = MollieApiClient::fake([
    GetPaymentRequest::class => MockResponse::notFound('No payment exists with token tr_xxxxxxxxxxx')
]);

// 422 Validation Error (with optional field reference)
$client = MollieApiClient::fake([
    CreatePaymentRequest::class => MockResponse::unprocessableEntity(
        detail: 'Amount must be at least €1.00',
        field: 'amount'
    )
]);
```

**Generic Error Builder**
```php
// Custom status code example
$response = MockResponse::error(
    status: 403,
    title: 'Forbidden',
    detail: 'Insufficient permissions to access this resource'
);

// Special characters handling
$detail = 'Invalid parameter "recurringType" - did you mean "sequenceType"?';
$response = MockResponse::unprocessableEntity($detail, 'field');
```

**Error Response Structure**
All errors follow Mollie's standardized format:
```json
{
    "status": 404,
    "title": "Not Found",
    "detail": "No payment exists with token tr_xxxxxxxxxxx",
    "field": "amount"  // Only present for validation errors
}
```

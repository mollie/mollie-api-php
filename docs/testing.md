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
API responses can be simulated without network calls by using the `MollieApiClient::fake()` method. Each Request/Response pair passed to the fake method is consumed after a matching request.

> [!NOTE]
> To keep pairs available for reuse after they've been matched, use the `retainRequests` parameter: `MollieApiClient::fake([...], retainRequests: true)`

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

### Sequence Mock Responses
To return different responses for the same request type, use `SequenceMockResponse` and pass `MockResponse` or `Closure` (which return `MockResponse` instances) in the order they should occur.

```php
$client = MollieApiClient::fake([
    DynamicGetRequest::class => new SequenceMockResponse(
        MockResponse::ok(['first' => 'response']),
        MockResponse::ok(['second' => 'response']),
        function (PendingRequest $pendingRequest) {
            //...

            return MockResponse::ok(['third' => 'response']);
        }
    )
])

To verify that a request was sent, use `assertSent` or `assertSentCount`.

```php
$client->send(new GetPaymentRequest('tr_xxxxxxxxxxxx', embedRefunds: true));

$client->assertSent(GetPaymentRequest::class);
$client->assertSent(function (PendingRequest $pendingRequest, Response $response) {
    return $pendingRequest->query()->get('embed') === 'refunds';
});

$client->assertSentCount(1);
```

### MockResponse Options
Configure responses using:
- **Arrays**: Direct data structure
- **Strings**: JSON payloads or predefined fixture names
- **Callables**: Dynamic response generation *or* intercepting assertions

```php
// Array response
MockResponse::created([
    'id' => 'tr_xxxxxxxxxxxx',
    'amount' => ['value' => '20.00', 'currency' => 'EUR']
]);

// Fixture response
MockResponse::created('payment');

// Dynamic response
MockResponse::created(function (PendingRequest $request) {
    return ['amount' => $request->hasParameter('amount') ? 10 : 20];
});

// Intercepting assertions
function (PendingRequest $request) use ($idempotencyKey) {
    $this->assertEquals($idempotencyKey, $request->headers()->get('Idempotency-Key'));

    return MockResponse::created('payment');
}
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
- Maintain resource relationships with a fluent interface

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

### Error Response Mocking
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

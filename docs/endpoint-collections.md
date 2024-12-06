# Introduction
Endpoint collections provide a fluent interface for interacting with Mollie's API. Each collection manages specific resource types like payments, customers, and subscriptions, offering methods to create, retrieve, update, and delete these resources.

## Setup

1. **Initialize the Mollie Client:**

```php
$mollie = new \Mollie\Api\MollieApiClient();
$mollie->setApiKey("test_*************************");
```

2. **Access Endpoints via the Client:**
```php
// Payments endpoint
$mollie->payments->...

// Customers endpoint
$mollie->customers->...

// Other endpoints
$mollie->balances->...
$mollie->orders->...
```

3. **Call methods**

**Simple: Using arrays**
This approach is direct but provides less type safety:
```php
$payment = $mollie->payments->create([
    'amount' => [
        'currency' => 'EUR',
        'value' => '10.00'
    ],
    'description' => 'My first API payment'
]);
```

**Advanced: Using typed payloads/queries**
This approach provides full IDE support and type safety:
```php
use Mollie\Api\Http\Payload\Money;
use Mollie\Api\Http\Payload\CreatePaymentPayload;

$payment = $mollie->payments->create(
    new CreatePaymentPayload(
        description: 'My first API payment',
        amount: new Money('EUR', '10.00')
    )
);
```

If you have an array and need to interact with the payload or query, you can use a dedicated factory to convert the array into a typed class.

use Mollie\Api\Http\Payload\Money;
use Mollie\Api\Factories\CreatePaymentPayloadFactory;

// Fully untyped data
$createPaymentPayload = CreatePaymentPayloadFactory::new([
    'amount' => [
        'currency' => 'EUR',
        'value' => '10.00'
    ],
    'description' => 'My first API payment'
]);

// Partially untyped
$createPaymentPayload = CreatePaymentPayloadFactory::new([
    'amount' => new Money('EUR', '10.00'),
    'description' => 'My first API payment'
]);

// Access payload
$createPaymentPayload->amount->...
```

This approach allows you to seamlessly convert arrays into typed objects, providing better IDE support and type safety while working with your data.

# Available Endpoints

## Balances

[Official Documentation](https://docs.mollie.com/reference/v2/balances-api/get-balance)

**Available Queries:**
- `GetPaginatedBalanceQuery` - For listing balances with pagination

### Balance Information

```php
// Get primary balance
$balance = $mollie->balances->primary();

// Get specific balance
$balance = $mollie->balances->get('bal_12345');

// List balances
$balances = $mollie->balances->page();
```

## Balance Reports

[Official Documentation](https://docs.mollie.com/reference/v2/balance-reports-api/get-balance-report)

**Available Queries:**
- `GetBalanceReportQuery` - For retrieving balance reports

### Balance Report Details

```php
// Get report for specific balance
$report = $mollie->balanceReports->getForId('bal_12345', [
    'from' => '2024-01-01',
    'until' => '2024-01-31',
    'grouping' => 'transaction-categories'
]);
```

## Balance Transactions

[Official Documentation](https://docs.mollie.com/reference/v2/balance-transactions-api/list-balance-transactions)

**Available Queries:**
- `GetPaginatedBalanceQuery` - For listing balance transactions with pagination

### Balance Transaction Management

```php
// Get transactions for a balance
$transactions = $mollie->balanceTransactions->pageFor($balance);

// Use iterator for all transactions
foreach ($mollie->balanceTransactions->iteratorFor($balance) as $transaction) {
    echo $transaction->id;
}
```

## Chargebacks

[Official Documentation](https://docs.mollie.com/reference/v2/chargebacks-api/get-chargeback)

**Available Queries:**
- `GetPaginatedChargebacksQuery` - For listing chargebacks with pagination

### Chargeback Management

```php
// Get all chargebacks
$chargebacks = $mollie->chargebacks->page();

// Use iterator for all chargebacks
foreach ($mollie->chargebacks->iterator() as $chargeback) {
    echo $chargeback->id;
}
```

## Clients

[Official Documentation](https://docs.mollie.com/reference/v2/clients-api/get-client)

**Available Queries:**
- `GetClientQuery` - For retrieving client details

### Client Management

```php
// Get a specific client
$client = $mollie->clients->get('org_12345678', [
    'testmode' => true
]);

// List all clients
$clients = $mollie->clients->page(
    from: 'org_12345678',
    limit: 50
);
```

## Customers

[Official Documentation](https://docs.mollie.com/reference/v2/customers-api/create-customer)

**Available Payloads:**
- `CreateCustomerPayload` - For creating new customers
- `UpdateCustomerPayload` - For updating existing customers

### Customer Management

```php
// Create a customer
$customer = $mollie->customers->create([
    'name' => 'John Doe',
    'email' => 'john@example.org',
]);

// Get a customer
$customer = $mollie->customers->get('cst_8wmqcHMN4U');

// Update a customer
$customer = $mollie->customers->update('cst_8wmqcHMN4U', [
    'name' => 'Updated Name'
]);

// Delete a customer
$mollie->customers->delete('cst_8wmqcHMN4U');

// List customers
$customers = $mollie->customers->page();
```

## Invoices

[Official Documentation](https://docs.mollie.com/reference/v2/invoices-api/get-invoice)

**Available Queries:**
- `GetPaginatedInvoiceQuery` - For listing invoices with pagination

### Invoice Management

```php
// Get a specific invoice
$invoice = $mollie->invoices->get('inv_xBEbP9rvAq');

// List all invoices
$invoices = $mollie->invoices->page(
    from: 'inv_xBEbP9rvAq',
    limit: 50
);
```

## Mandates

[Official Documentation](https://docs.mollie.com/reference/v2/mandates-api/create-mandate)

**Available Payloads:**
- `CreateMandatePayload` - For creating new mandates

### Mandate Management

```php
// Create a mandate for a customer
$mandate = $mollie->mandates->createFor($customer, [
    'method' => \Mollie\Api\Types\PaymentMethod::DIRECTDEBIT,
    'consumerName' => 'John Doe',
    'consumerAccount' => 'NL55INGB0000000000',
    'consumerBic' => 'INGBNL2A',
    'signatureDate' => '2024-01-01',
    'mandateReference' => 'YOUR-COMPANY-MD13804'
]);

// Get a mandate
$mandate = $mollie->mandates->getFor($customer, 'mdt_h3gAaD5zP');

// Revoke a mandate
$mollie->mandates->revokeFor($customer, 'mdt_h3gAaD5zP');

// List mandates
$mandates = $mollie->mandates->pageFor($customer);
```

## Methods

[Official Documentation](https://docs.mollie.com/reference/v2/methods-api/get-method)

**Available Queries:**
- `GetPaymentMethodQuery` - For retrieving method details
- `GetAllMethodsQuery` - For listing all available methods
- `GetEnabledPaymentMethodsQuery` - For listing enabled methods

### Payment Methods

```php
// Get a method
$method = $mollie->methods->get(\Mollie\Api\Types\PaymentMethod::IDEAL);

// List all methods
$methods = $mollie->methods->all();

// List enabled methods
$methods = $mollie->methods->allEnabled([
    'amount' => [
        'currency' => 'EUR',
        'value' => '100.00'
    ]
]);
```

### Method Issuers

```php
// Enable an issuer
$issuer = $mollie->methodIssuers->enable(
    'pfl_v9hTwCvYqw',
    \Mollie\Api\Types\PaymentMethod::IDEAL,
    'ideal_INGBNL2A'
);

// Disable an issuer
$mollie->methodIssuers->disable(
    'pfl_v9hTwCvYqw',
    \Mollie\Api\Types\PaymentMethod::IDEAL,
    'ideal_INGBNL2A'
);
```

## Onboarding

[Official Documentation](https://docs.mollie.com/reference/v2/onboarding-api/get-onboarding-status)

**Available Queries:**
- `GetOnboardingStatusQuery` - For retrieving onboarding status

### Onboarding Management

```php
// Get onboarding status
$onboarding = $mollie->onboarding->status();
```

## Organizations

[Official Documentation](https://docs.mollie.com/reference/v2/organizations-api/get-organization)

**Available Queries:**
- `GetOrganizationQuery` - For retrieving organization details

### Organization Management

```php
// Get an organization
$organization = $mollie->organizations->get('org_12345678');

// Get current organization
$organization = $mollie->organizations->current();

// Get partner status
$partner = $mollie->organizations->partnerStatus();
```

## Permissions

[Official Documentation](https://docs.mollie.com/reference/v2/permissions-api/get-permission)

**Available Queries:**
- `GetPermissionQuery` - For retrieving permission details

### Permission Management

```php
// Get a permission
$permission = $mollie->permissions->get('payments.read');

// List all permissions
$permissions = $mollie->permissions->list();
```

## Payments

[Official Documentation](https://docs.mollie.com/reference/v2/payments-api/create-payment)

**Available Payloads:**
- `CreatePaymentPayload` - For creating new payments
- `UpdatePaymentPayload` - For updating existing payments

**Available Queries:**
- `GetPaymentQuery` - For retrieving payments with optional embeds
- `GetPaginatedPaymentsQuery` - For listing payments with pagination

### Payment Management

```php
// Create a payment using typed payload
use Mollie\Api\Http\Payload\Money;
use Mollie\Api\Http\Payload\CreatePaymentPayload;

$payload = new CreatePaymentPayload(
    description: 'Order #12345',
    amount: new Money('EUR', '10.00'),
    redirectUrl: 'https://webshop.example.org/order/12345/',
    webhookUrl: 'https://webshop.example.org/mollie-webhook/'
);

$payment = $mollie->payments->create($payload);

// Get a payment
$payment = $mollie->payments->get('tr_7UhSN1zuXS');

// Update a payment
$payment = $mollie->payments->update('tr_7UhSN1zuXS', [
    'description' => 'Updated description'
]);

// Cancel a payment
$mollie->payments->cancel('tr_7UhSN1zuXS');

// List payments
$payments = $mollie->payments->page();
```

### Payment Links

[Official Documentation](https://docs.mollie.com/reference/v2/payment-links-api/create-payment-link)

**Available Payloads:**
- `CreatePaymentLinkPayload` - For creating payment links
- `UpdatePaymentLinkPayload` - For updating payment links

### Payment Captures

[Official Documentation](https://docs.mollie.com/reference/v2/captures-api/get-capture)

**Available Payloads:**
- `CreatePaymentCapturePayload` - For creating captures

**Available Queries:**
- `GetPaymentCaptureQuery` - For retrieving capture details
- `GetPaginatedPaymentCapturesQuery` - For listing captures

### Payment Chargebacks

```php
// Get a chargeback
$chargeback = $mollie->paymentChargebacks->getFor($payment, 'chb_n9z0tp');

// List chargebacks for payment
$chargebacks = $mollie->paymentChargebacks->pageFor($payment);
```

### Payment Routes

```php
// Update release date for a route
$route = $mollie->paymentRoutes->updateReleaseDateFor(
    $payment,
    'rt_abc123',
    '2024-01-01'
);
```

## Profiles

[Official Documentation](https://docs.mollie.com/reference/v2/profiles-api/create-profile)

**Available Payloads:**
- `CreateProfilePayload` - For creating new profiles

**Available Factories:**
- `ProfileFactory` - For creating profile instances

### Profile Management

```php
// Create a profile
$profile = $mollie->profiles->create(new CreateProfilePayload(
    'My Test Profile',
    'https://example.org',
    'info@example.org',
    '+31612345678',
    'test'
));

// Get a profile
$profile = $mollie->profiles->get('pfl_v9hTwCvYqw');

// Get current profile
$profile = $mollie->profiles->getCurrent();

// Update a profile
$profile = $mollie->profiles->update('pfl_v9hTwCvYqw', [
    'name' => 'Updated Profile Name'
]);

// Delete a profile
$mollie->profiles->delete('pfl_v9hTwCvYqw');

// List profiles
$profiles = $mollie->profiles->page();
```

### Profile Methods

```php
// Enable a method
$method = $mollie->profileMethods->enable(
    'pfl_v9hTwCvYqw',
    \Mollie\Api\Types\PaymentMethod::IDEAL
);

// Disable a method
$mollie->profileMethods->disable(
    'pfl_v9hTwCvYqw',
    \Mollie\Api\Types\PaymentMethod::IDEAL
);
```

## Refunds

[Official Documentation](https://docs.mollie.com/reference/v2/refunds-api/create-refund)

**Available Payloads:**
- `CreateRefundPaymentPayload` - For creating refunds on payments
- `CreateRefundOrderPayload` - For creating refunds on orders

**Available Queries:**
- `GetPaginatedRefundsQuery` - For listing refunds with pagination

### Refund Management

```php
// Create a refund for a payment
$refund = $mollie->refunds->createForPayment($paymentId, [
    'amount' => [
        'currency' => 'EUR',
        'value' => '15.00'
    ],
    'description' => 'Refund for returned item'
]);

// List refunds
$refunds = $mollie->refunds->page();
```

## Sessions

[Official Documentation](https://docs.mollie.com/reference/v2/sessions-api/create-session)

**Available Payloads:**
- `CreateSessionPayload` - For creating sessions

**Available Queries:**
- `GetPaginatedSessionsQuery` - For listing sessions with pagination

### Session Management

```php
// Create a session
$session = $mollie->sessions->create([
    'amount' => [
        'currency' => 'EUR',
        'value' => '100.00'
    ],
    'description' => 'Session for service'
]);

// Get a session
$session = $mollie->sessions->get('sessionId');
```

## Settlements

[Official Documentation](https://docs.mollie.com/reference/v2/settlements-api/get-settlement)

**Available Queries:**
- `GetPaginatedSettlementsQuery` - For listing settlements with pagination

### Settlement Management

```php
// Get a settlement
$settlement = $mollie->settlements->get('settlementId');

// List settlements
$settlements = $mollie->settlements->page();
```

### Settlement Captures

```php
// Get captures for a settlement
$captures = $mollie->settlementCaptures->pageFor($settlement);

// Use iterator
foreach ($mollie->settlementCaptures->iteratorFor($settlement) as $capture) {
    echo $capture->id;
}
```

### Settlement Chargebacks

```php
// List chargebacks for a settlement
$chargebacks = $mollie->settlementChargebacks->pageFor($settlement);

// Use iterator
foreach ($mollie->settlementChargebacks->iteratorFor($settlement) as $chargeback) {
    echo $chargeback->id;
}
```

### Settlement Payments

```php
// List payments in a settlement
$payments = $mollie->settlementPayments->pageFor($settlement);

// Use iterator
foreach ($mollie->settlementPayments->iteratorFor($settlement) as $payment) {
    echo $payment->id;
}
```

## Subscriptions

[Official Documentation](https://docs.mollie.com/reference/v2/subscriptions-api/create-subscription)

**Available Payloads:**
- `CreateSubscriptionPayload` - For creating subscriptions

**Available Queries:**
- `GetAllPaginatedSubscriptionsQuery` - For listing all subscriptions

### Subscription Management

```php
// Create a subscription
$subscription = $mollie->subscriptions->createForCustomer('customerId', [
    'amount' => [
        'currency' => 'EUR',
        'value' => '25.00'
    ],
    'interval' => '1 month',
    'description' => 'Monthly subscription'
]);

// List subscriptions
$subscriptions = $mollie->subscriptions->pageForCustomer('customerId');
```

## Terminals

[Official Documentation](https://docs.mollie.com/reference/v2/terminals-api/get-terminal)

**Available Queries:**
- `GetPaginatedTerminalsQuery` - For listing terminals with pagination

### Terminal Management

```php
// Get a terminal
$terminal = $mollie->terminals->get('terminalId');

// List terminals
$terminals = $mollie->terminals->page();
```

## Wallets

[Official Documentation](https://docs.mollie.com/reference/v2/wallets-api/request-apple-pay-payment-session)

**Available Payloads:**
- `RequestApplePayPaymentSessionPayload` - For requesting Apple Pay payment sessions

### Wallet Management

```php
// Request an Apple Pay payment session
$session = $mollie->wallets->requestApplePayPaymentSession([
    'domainName' => 'example.com',
    'validationUrl' => 'https://apple-pay-gateway.apple.com/paymentservices/startSession'
]);
```

## Common Patterns

### Pagination

Most list methods support pagination:

```php
// Get first page
$payments = $mollie->payments->page();

// Get specific page
$payments = $mollie->payments->page(
    from: 'tr_7UhSN1zuXS',  // Start from this ID
    limit: 50               // Items per page
);

// Get all items using iterator
foreach ($mollie->payments->iterator() as $payment) {
    echo $payment->id;
}
```

### Error Handling

Handle errors using `ApiException`:

```php
try {
    $payment = $mollie->payments->get('tr_xxx');
} catch (\Mollie\Api\Exceptions\ApiException $e) {
    echo "API call failed: {$e->getMessage()}";
}
```

## Common Data Types

**Shared Payloads:**
- `Money` - For handling currency amounts
- `Address` - For handling address information
- `Metadata` - For handling custom metadata
- `DataCollection` - For handling collections of data
- `ApplicationFee` - For handling application fees

**Base Classes:**
- `DataBag` - Base class for payload objects
- `Query` - Base class for query objects

## Factories

**Query Factories:**
- `PaginatedQueryFactory` - For creating paginated queries
- `SortablePaginatedQueryFactory` - For creating sortable paginated queries

**Payload Factories:**
- `CreatePaymentPayloadFactory` - For creating payment payloads
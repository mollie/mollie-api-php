# Request Reference

This document provides a quick reference index of all available request classes in the Mollie PHP SDK. For detailed parameter information, see the PHPDoc comments in each request class. Request classes provide a type-safe way to interact with the Mollie API directly, offering better IDE support and type checking compared to using endpoint collections with arrays.

## Payments

- **CreatePaymentRequest** - Create a new payment
- **GetPaymentRequest** - Retrieve a payment by ID
- **UpdatePaymentRequest** - Update an existing payment
- **CancelPaymentRequest** - Cancel a payment
- **CreatePaymentRefundRequest** - Create a refund for a payment
- **GetPaymentRefundRequest** - Retrieve a payment refund
- **CancelPaymentRefundRequest** - Cancel a payment refund
- **GetPaginatedPaymentsRequest** - List payments with pagination
- **ReleasePaymentAuthorizationRequest** - Release payment authorization

## Customers

- **CreateCustomerRequest** - Create a new customer
- **GetCustomerRequest** - Retrieve a customer by ID
- **UpdateCustomerRequest** - Update an existing customer
- **DeleteCustomerRequest** - Delete a customer
- **GetPaginatedCustomerRequest** - List customers with pagination
- **CreateCustomerPaymentRequest** - Create a payment for a customer
- **GetPaginatedCustomerPaymentsRequest** - List customer payments with pagination

## Subscriptions

- **CreateSubscriptionRequest** - Create a subscription for a customer
- **GetSubscriptionRequest** - Retrieve a subscription
- **UpdateSubscriptionRequest** - Update a subscription
- **CancelSubscriptionRequest** - Cancel a subscription
- **GetPaginatedSubscriptionsRequest** - List subscriptions with pagination
- **GetAllPaginatedSubscriptionsRequest** - List all subscriptions (across all customers)
- **GetPaginatedSubscriptionPaymentsRequest** - List subscription payments with pagination

## Mandates

- **CreateMandateRequest** - Create a mandate for a customer
- **GetMandateRequest** - Retrieve a mandate
- **RevokeMandateRequest** - Revoke a mandate
- **GetPaginatedMandateRequest** - List mandates with pagination

## Refunds

- **GetPaginatedRefundsRequest** - List refunds with pagination

## Webhooks

- **CreateWebhookRequest** - Create a webhook
- **GetWebhookRequest** - Retrieve a webhook
- **UpdateWebhookRequest** - Update a webhook
- **DeleteWebhookRequest** - Delete a webhook
- **TestWebhookRequest** - Test a webhook
- **GetPaginatedWebhooksRequest** - List webhooks with pagination
- **GetWebhookEventRequest** - Retrieve a webhook event

## Profiles

- **CreateProfileRequest** - Create a profile
- **GetProfileRequest** - Retrieve a profile
- **GetCurrentProfileRequest** - Retrieve the current profile
- **UpdateProfileRequest** - Update a profile
- **DeleteProfileRequest** - Delete a profile
- **GetPaginatedProfilesRequest** - List profiles with pagination

## Methods

- **GetMethodRequest** - Retrieve a payment method
- **GetAllMethodsRequest** - Retrieve all payment methods
- **GetEnabledMethodsRequest** - Retrieve all enabled payment methods
- **EnableMethodRequest** - Enable a payment method
- **DisableMethodRequest** - Disable a payment method
- **EnableMethodIssuerRequest** - Enable a method issuer
- **DisableMethodIssuerRequest** - Disable a method issuer

## Balances

- **GetBalanceRequest** - Retrieve a balance
- **GetPaginatedBalanceRequest** - List balances with pagination
- **GetBalanceReportRequest** - Retrieve a balance report
- **GetPaginatedBalanceTransactionRequest** - List balance transactions with pagination

## Settlements

- **GetSettlementRequest** - Retrieve a settlement
- **GetPaginatedSettlementsRequest** - List settlements with pagination
- **GetPaginatedSettlementPaymentsRequest** - List settlement payments with pagination
- **GetPaginatedSettlementRefundsRequest** - List settlement refunds with pagination
- **GetPaginatedSettlementCapturesRequest** - List settlement captures with pagination
- **GetPaginatedSettlementChargebacksRequest** - List settlement chargebacks with pagination

## Payment Links

- **CreatePaymentLinkRequest** - Create a payment link
- **GetPaymentLinkRequest** - Retrieve a payment link
- **UpdatePaymentLinkRequest** - Update a payment link
- **DeletePaymentLinkRequest** - Delete a payment link
- **GetPaginatedPaymentLinksRequest** - List payment links with pagination
- **GetPaginatedPaymentLinkPaymentsRequest** - List payment link payments with pagination

## Sessions

- **CreateSessionRequest** - Create a checkout session
- **GetSessionRequest** - Retrieve a session
- **UpdateSessionRequest** - Update a session
- **CancelSessionRequest** - Cancel a session
- **GetPaginatedSessionsRequest** - List sessions with pagination

## Invoices

- **GetInvoiceRequest** - Retrieve an invoice
- **GetPaginatedInvoiceRequest** - List invoices with pagination

## Sales Invoices

- **CreateSalesInvoiceRequest** - Create a sales invoice
- **GetSalesInvoiceRequest** - Retrieve a sales invoice
- **UpdateSalesInvoiceRequest** - Update a sales invoice
- **DeleteSalesInvoiceRequest** - Delete a sales invoice
- **GetPaginatedSalesInvoicesRequest** - List sales invoices with pagination

## Payment Routes

- **CreateDelayedPaymentRouteRequest** - Create a delayed payment route
- **ListPaymentRoutesRequest** - List payment routes
- **UpdatePaymentRouteRequest** - Update a payment route

## Payment Captures

- **CreatePaymentCaptureRequest** - Create a payment capture
- **GetPaymentCaptureRequest** - Retrieve a payment capture
- **GetPaginatedPaymentCapturesRequest** - List payment captures with pagination

## Payment Chargebacks

- **GetPaymentChargebackRequest** - Retrieve a payment chargeback
- **GetPaginatedPaymentChargebacksRequest** - List payment chargebacks with pagination
- **GetPaginatedChargebacksRequest** - List all chargebacks with pagination

## Other Resources

- **GetClientRequest** - Retrieve a client
- **GetPaginatedClientRequest** - List clients with pagination
- **CreateClientLinkRequest** - Create a client link
- **GetCapabilityRequest** - Retrieve a capability
- **ListCapabilitiesRequest** - List capabilities
- **GetPermissionRequest** - Retrieve a permission
- **ListPermissionsRequest** - List permissions
- **GetTerminalRequest** - Retrieve a terminal
- **GetPaginatedTerminalsRequest** - List terminals with pagination
- **GetOrganizationRequest** - Retrieve an organization
- **GetOnboardingStatusRequest** - Get onboarding status
- **GetOrganizationPartnerStatusRequest** - Get organization partner status
- **CreateConnectBalanceTransferRequest** - Create a Connect balance transfer
- **GetConnectBalanceTransferRequest** - Retrieve a Connect balance transfer
- **ListConnectBalanceTransfersRequest** - List Connect balance transfers
- **ApplePayPaymentSessionRequest** - Request Apple Pay payment session

## Using Request Classes

All request classes are located in the `Mollie\Api\Http\Requests` namespace.

**Example:**
```php
use Mollie\Api\Http\Requests\CreatePaymentRequest;
use Mollie\Api\Http\Data\Money;

$request = new CreatePaymentRequest(
    description: 'Order #12345',
    amount: Money::euro('10.00'),
    redirectUrl: 'https://example.com/redirect',
    webhookUrl: 'https://example.com/webhook'
);

$payment = $mollie->send($request);
```

For detailed parameter information, use your IDE's autocomplete features, or refer to the [Official API documentation](https://docs.mollie.com/reference/overview).

## See Also

- [Requests Documentation](requests.md) - General information about using requests
- [Endpoint Collections](endpoint-collections.md) - Alternative way to interact with the API
- [Mollie API Reference](https://docs.mollie.com/reference/overview) - Official API documentation

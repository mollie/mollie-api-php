# Mollie API PHP Recipes

This directory contains recipes for common use cases with the Mollie API PHP client. Each recipe provides a practical example of how to use the API client for a specific task.

## Structure

The recipes are organized by resource type:
- `payments/` - Payment-related operations (create, update, refund, etc.)
- `customers/` - Customer management
- `mandates/` - Mandate operations
- `subscriptions/` - Subscription handling
- `captures/` - Payment capture operations
- `chargebacks/` - Chargeback handling
- `refunds/` - Refund operations
- `webhooks/` - Webhook management and events

Each recipe includes:
- Complete code example
- Example response fields
- Additional notes and considerations

## Getting Started

Before using any recipe, make sure you have properly initialized the API client:

```php
use Mollie\Api\MollieApiClient;

$mollie = new MollieApiClient;
$mollie->setApiKey('test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM');
```

# Idempotency

## Overview

Idempotency ensures that multiple identical requests to an API result in the same outcome without creating duplicate resources or effects. This is crucial for operations that involve financial transactions to prevent unintended charges or state changes.

Mollie API supports idempotent requests for critical operations such as creating payments or refunds. This is automatically managed by the API client using the `ApplyIdempotencyKey` middleware.

For more detailed information, refer to the [Mollie API Idempotency Documentation](https://docs.mollie.com/reference/api-idempotency).

> [!Note]
> This package automatically handles idempotency for you. The information below allows you to override the default idempotency behavior.

## Automatic Idempotency Key Handling

The Mollie API client automatically handles idempotency for mutating requests (POST, PATCH, DELETE) through the `ApplyIdempotencyKey` middleware. This middleware checks if the request is a mutating type and applies an idempotency key if one is not already provided.

### How It Works

1. **Check Request Type**: The middleware checks if the request method is POST, PATCH, or DELETE.
2. **Apply Idempotency Key**: If the request is mutating, the middleware will:
   - Use a custom idempotency key if provided.
   - Otherwise, generate a key using the configured `IdempotencyKeyGenerator` if available.
   - If no generator is set and no key is provided, no idempotency key is applied.

### Customizing Idempotency Key

You can customize how idempotency keys are generated or applied in several ways:

- **Provide a Custom Key**: Manually set an idempotency key for a specific request.
- **Use a Custom Generator**: Implement the `IdempotencyKeyGeneratorContract` to provide a custom method of generating idempotency keys.

#### Example: Setting a Custom Key

```php
$mollie->setIdempotencyKey('your_custom_key_here');
```

#### Example: Using a Custom Key Generator

Implement your key generator:

```php
class MyCustomKeyGenerator implements IdempotencyKeyGeneratorContract {
    public function generate(): string {
        return 'custom_' . bin2hex(random_bytes(10));
    }
}

$mollie->setIdempotencyKeyGenerator(new MyCustomKeyGenerator());
```

## Best Practices

- **Unique Keys**: Ensure that each idempotency key is unique to each operation. Typically, UUIDs are used for this purpose.
- **Handling Errors**: Handle errors gracefully, especially when an API call with an idempotency key fails. Check the error message to understand whether you should retry the request with the same key or a new key.

## Conclusionz

Using idempotency keys in your API requests to Mollie can significantly enhance the reliability of your payment processing system, ensuring that payments are not unintentionally duplicated and that your application behaves predictably even in the face of network and service interruptions.

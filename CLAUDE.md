# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

PHP SDK for the Mollie API (https://docs.mollie.com/reference/overview). Supports PHP 7.4–8.4. Namespace: `Mollie\Api`.

## Commands

```bash
# Run all tests (parallel)
composer test                    # or: vendor/bin/paratest --verbose

# Run a single test file
vendor/bin/phpunit tests/Path/To/TestFile.php

# Run a single test method
vendor/bin/phpunit --filter testMethodName tests/Path/To/TestFile.php

# Static analysis (level 5)
vendor/bin/phpstan

# Install dependencies
composer install
```

## Architecture

This SDK has two interaction layers:

### 1. Modern Request Layer (preferred for new code)
Send typed request objects directly via `$client->send()`. Inspired by SaloonPHP.

```
src/Http/Requests/       → Typed request classes (CreatePaymentRequest, GetPaymentRequest, etc.)
src/Http/Request.php     → Abstract base class (ResourceHydratableRequest, CollectionHydratableRequest)
src/Http/PendingRequest  → Orchestrates the request pipeline
src/Http/Middleware/      → Request/response middleware (auth, hydration, error handling, idempotency)
src/Http/Adapter/        → HTTP client adapters (Guzzle, cURL, PSR-18)
```

### 2. Legacy EndpointCollection Layer
Fluent interface via magic properties on the client (`$client->payments->create(...)`). Delegates to the modern request layer via Factories.

```
src/EndpointCollection/  → Fluent endpoint classes (PaymentEndpointCollection, etc.)
src/Factories/           → Convert unstructured array data → typed Request objects
```

The `HasEndpoints` trait provides `__get()` magic to lazy-load endpoint collections.

### Supporting Code

```
src/MollieApiClient.php  → Main entry point, composed via traits
src/Resources/           → Response models (Payment, Customer, etc.) with convenience methods
src/Contracts/           → Interfaces for extensibility (Connector, HttpAdapterContract, etc.)
src/Traits/              → Shared behavior (HandlesAuthentication, SendsRequests, HasMiddleware, etc.)
src/Types/               → Constants and enums (PaymentStatus, PaymentMethod, etc.)
src/Exceptions/          → Custom exception hierarchy (ApiException → specific subtypes)
src/Http/Data/           → Value objects (Money, Address, OrderLine, etc.)
src/Fake/                → Testing utilities (MockMollieClient, MockResponse)
```

### Request Execution Flow

```
MollieApiClient::send(Request)
  → PendingRequest applies handlers (user-agent, headers, auth, testmode)
  → Middleware pipeline (onRequest)
  → HttpAdapter sends HTTP request
  → Middleware pipeline (onResponse: error conversion, hydration)
  → Returns hydrated Resource object
```

### Adding a New Endpoint

1. Create Request class in `src/Http/Requests/` (extend `ResourceHydratableRequest` or `CollectionHydratableRequest`)
2. Create Factory class in `src/Factories/` to bridge array input → typed request
3. Add method to the appropriate `EndpointCollection` in `src/EndpointCollection/`
4. Add Resource class in `src/Resources/` if it's a new resource type

### Testing Pattern

Use the built-in mock client — no real HTTP calls needed:

```php
$client = MollieApiClient::fake([
    GetPaymentRequest::class => MockResponse::ok(['resource' => 'payment', ...]),
]);

$payment = $client->send(new GetPaymentRequest('tr_xxx'));

$client->assertSent(GetPaymentRequest::class);
```

Test fixtures live in `tests/Fixtures/`. Documentation and code examples are in `docs/`.

## CI

- **Tests**: PHPUnit via Paratest across PHP 7.4, 8.0, 8.1, 8.2, 8.3, 8.4
- **Static analysis**: PHPStan level 5 (with baseline)
- **Code style**: PHP CS Fixer

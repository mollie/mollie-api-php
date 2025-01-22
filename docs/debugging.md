# Debugging with Mollie API Client

## Overview

The Mollie API client provides powerful debugging capabilities to help you inspect and troubleshoot API requests and responses. The debugging functionality is implemented through middleware and automatically sanitizes sensitive data to prevent accidental exposure of credentials.

## Basic Usage

### Enable All Debugging

To enable both request and response debugging:

```php
$mollie = new \Mollie\Api\MollieApiClient();
$mollie->debug(); // Enables both request and response debugging
```

### Enable on Request

To enable debugging for a specific request:

```php
$request = new CreatePaymentRequest(...);

// enable output for request and response
$mollie->send($request->debug());

// only debug request
$mollie->send($request->debugRequest(die: true));

// only debug response
$mollie->send($request->debugResponse(die: true));
```

### Debug Specific Parts

You can choose to debug only requests or only responses:

```php
// Debug only requests
$mollie->debugRequest();

// Debug only responses
$mollie->debugResponse();
```

## Custom Debuggers

You can provide your own debugging functions to customize how debugging information is displayed:

```php
// Custom request debugger
$mollie->debugRequest(function($pendingRequest, $psrRequest) {
    // Your custom debugging logic here
});

// Custom response debugger
$mollie->debugResponse(function($response, $psrResponse) {
    // Your custom debugging logic here
});
```

## Security Features

### Automatic Sanitization

When debugging is enabled, the client automatically:
- Removes sensitive headers (Authorization, User-Agent, etc.)
- Sanitizes request data to prevent credential exposure
- Handles exceptions safely by removing sensitive data

### Die After Debug

For development purposes, you can halt execution after debugging output:

```php
$mollie->debug(die: true); // Will stop execution after debugging output
```

## Best Practices

1. **Development Only**: Never enable debugging in production environments
2. **Custom Debuggers**: When implementing custom debuggers, ensure they handle sensitive data appropriately
3. **Exception Handling**: Debug mode works with exceptions, helping you troubleshoot API errors safely

## Example Usage

```php
try {
    $mollie = new \Mollie\Api\MollieApiClient();
    $mollie->setApiKey("test_dHar4XY7LxsDOtmnkVtjNVWXLSlXsM");

    // Enable debugging for development
    $mollie->debug();

    // Your API calls here
    $payment = $mollie->payments->create([...]);

} catch (\Mollie\Api\Exceptions\ApiException $e) {
    // Exception will include sanitized debug information
    echo "API call failed: " . htmlspecialchars($e->getMessage());
}
```

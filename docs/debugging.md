# Debugging with Mollie API Client

## Overview

The Mollie API client provides debugging callbacks to help you inspect and troubleshoot API requests and responses. The built-in Symfony VarDumper debugger prints request and response details for local development. Exception handling sanitizes sensitive request data before it is attached to thrown exceptions, but default debug output itself is not a production-safe logging surface.

## Basic Usage

### Enable All Debugging

To enable both request and response debugging:

```php
$mollie = new \Mollie\Api\MollieApiClient();
$mollie->debug(); // Enables both request and response debugging
```

The default debugger requires `symfony/var-dumper`. If that package is not installed, calling the default debugger throws an actionable runtime exception. Pass custom callbacks to `debugRequest()` or `debugResponse()` if you want to avoid that optional dependency.

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

When an exception is thrown while debugging is enabled, the client sanitizes the request data attached to the exception. The default request/response debug callbacks are for local inspection and may print headers and request bodies. Treat their output as sensitive.

### Die After Debug

For development purposes, you can halt execution after debugging output:

```php
$mollie->debug(die: true); // Will stop execution after debugging output
```

## Best Practices

1. **Development Only**: Never enable debugging in production environments
2. **Custom Debuggers**: When implementing custom debuggers, redact credentials and personal data before writing to logs
3. **Exception Handling**: Debug mode works with exceptions, and exception request data is sanitized before being exposed

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

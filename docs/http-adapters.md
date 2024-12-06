# HTTP Adapters

## Overview

An HTTP adapter is a component that manages the communication between your application and an API. It abstracts the details of making HTTP requests and handling responses, allowing you to use different HTTP clients (like Guzzle, cURL, or custom clients) interchangeably without changing the way you interact with the API.

## MollieHttpAdapterPicker

The `MollieHttpAdapterPicker` is responsible for selecting the appropriate HTTP adapter based on the environment or the provided HTTP client. If no client is specified in the `MollieApiClient` constructor, it picks a default adapter.

### How It Works

1. **No Client Specified**: If no client is provided, it checks if Guzzle is available and picks the appropriate version of the Guzzle adapter.
2. **Custom Client Provided**: If a custom client is provided and it implements the `HttpAdapterContract`, it is used directly. If it's a Guzzle client, it is wrapped in a `GuzzleMollieHttpAdapter`.
3. **Unrecognized Client**: Throws an `UnrecognizedClientException` if the client is not recognized.

## Creating a Custom Adapter

To create a custom HTTP adapter:
1.	Implement HttpAdapterContract.
2.	Use HasDefaultFactories Trait to simplify adapter implementation.

```php
use Mollie\Api\Contracts\HttpAdapterContract;
use Mollie\Api\Traits\HasDefaultFactories;

class MyCustomHttpAdapter implements HttpAdapterContract {
    use HasDefaultFactories;

    public function sendRequest(PendingRequest $pendingRequest): Response {
        // Implementation for sending HTTP request
    }

    public function version(): ?string {
        return 'my-custom-adapter/1.0';
    }
}
```

### Debugging

When debugging mode is enabled, adapters remove sensitive information before they fire an exception.

Adapters that support debugging must implement the `SupportsDebuggingContract`. This contract defines methods `enableDebugging()` and `disableDebugging()` to control debugging behavior.

```php
use Mollie\Api\Contracts\SupportsDebuggingContract;

class MyCustomHttpAdapter implements HttpAdapterContract, SupportsDebuggingContract {
    // Implementation of debugging methods
}
```

## Available Adapters

Out of the box, the Mollie API client provides several adapters:

- **GuzzleMollieHttpAdapter**: Wraps a Guzzle HTTP client for sending requests.
- **CurlMollieHttpAdapter**: Uses cURL for sending HTTP requests. This is the default if Guzzle is not available.

## Enabling Debugging

Debugging can be enabled through the `HandlesDebugging` trait. This trait allows you to toggle debugging on the HTTP client, which is useful for development and troubleshooting.

### How to Enable Debugging

1. **Enable Debugging**: Call `enableDebugging()` on the Mollie API client instance. This sets the debugging mode on the underlying HTTP adapter, if it supports debugging.

2. **Disable Debugging**: Call `disableDebugging()` to turn off debugging.

```php
$mollieClient->enableDebugging();
$mollieClient->disableDebugging();
```

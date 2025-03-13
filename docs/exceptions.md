# Exceptions

This document describes the exceptions that can be thrown by the Mollie API PHP client.

## Exception Hierarchy

### `MollieException`

The abstract base class for **all** exceptions in the Mollie API PHP client.

### `RequestException`

The `RequestException` serves as the base for all request-related exceptions.

### `ApiException`

Extends `RequestException` and serves as the base for all API-related exceptions. This is the parent class for all specific HTTP status code exceptions. This exception is also thrown when the Mollie API returns an error response that doesn't match any of the more specific exceptions below.

Properties and methods:
- `getDocumentationUrl()`: Returns the URL to the documentation for this error, if available
- `getDashboardUrl()`: Returns the URL to the dashboard for this error, if available
- `getRaisedAt()`: Returns the timestamp when the exception was raised
- `getPlainMessage()`: Returns the plain exception message without timestamp and metadata

## HTTP Status Code Exceptions

The following exceptions all extend `ApiException` and are thrown based on specific HTTP status codes returned by the API:

### `UnauthorizedException` (401)

Thrown when authentication fails. This typically happens when using an invalid API key.

### `ForbiddenException` (403)

Thrown when the request is understood but refused due to permission issues.

### `NotFoundException` (404)

Thrown when the requested resource does not exist.

### `MethodNotAllowedException` (405)

Thrown when the HTTP method used is not allowed for the requested endpoint.

### `RequestTimeoutException` (408)

Thrown when the request times out.

### `ValidationException` (422)

Thrown when the request data fails validation. This typically happens when required fields are missing or have invalid values.

### `TooManyRequestsException` (429)

Thrown when rate limiting is applied because too many requests were made in a short period.

### `ServiceUnavailableException` (503)

Thrown when the Mollie API service is temporarily unavailable.

## Network Exceptions

### `NetworkRequestException`

Thrown when a network error occurs during the request.

### `RetryableNetworkRequestException`

Thrown when a network error occurs that might be resolved by retrying the request.

## Other Exceptions

### `ClientException`

Base exception for client-related errors.

### `EmbeddedResourcesNotParseableException`

Thrown when embedded resources cannot be parsed.

### `IncompatiblePlatformException`

Thrown when the platform is incompatible with the Mollie API client.

### `InvalidAuthenticationException`

Thrown when the authentication method is invalid.

### `JsonParseException`

Thrown when JSON parsing fails.

### `LogicException`

Thrown when there is a logical error in the client code.

### `MissingAuthenticationException`

Thrown when no authentication method is provided.

### `ServerException`

Thrown when a server error occurs.

### `UnrecognizedClientException`

Thrown when the client is not recognized by the Mollie API.

## Handling Exceptions

Here's an example of how to handle exceptions:

```php
try {
    $payment = $mollie->payments->get("tr_xxx");
} catch (Mollie\Api\Exceptions\UnauthorizedException $e) {
    // Invalid API key
    echo "Invalid API key: " . $e->getMessage();
} catch (Mollie\Api\Exceptions\NotFoundException $e) {
    // Payment not found
    echo "Payment not found: " . $e->getMessage();
} catch (Mollie\Api\Exceptions\MollieException $e) {
    // Other Mollie error
    echo "Mollie error: " . $e->getMessage();
}
```

You can also catch all Mollie exceptions at once:

```php
try {
    $payment = $mollie->payments->get("tr_xxx");
} catch (Mollie\Api\Exceptions\MollieException $e) {
    // Any Mollie error
    echo "Mollie error: " . $e->getMessage();
}
```

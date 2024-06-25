<?php

namespace Mollie\Api\Http;

use Mollie\Api\Contracts\ResponseContract;
use Mollie\Api\Exceptions\ApiException;

class ResponseHandler
{
    /**
     * @param ResponseContract|null $response
     * @param string|null $requestBody
     * @return ResponseContract
     */
    public function handle(?ResponseContract $response = null, ?string $requestBody = null): ResponseContract
    {
        if ($response === null) {
            return new NoResponse();
        }

        $this->guard($response);

        $this->throwExceptionIfRequestFailed($response, $requestBody);

        return $response;
    }

    public static function create(): self
    {
        return new static();
    }

    public static function noResponse(): ResponseContract
    {
        return (new static())->handle(null);
    }

    protected function guard(ResponseContract $response): void
    {
        $this->guardNoContentWithBody($response);
        $this->guardJsonNotDecodable($response);

        // @todo check if this is still necessary as it seems to be from api v1
        if (isset($response->json()->error)) {
            throw new ApiException($response->json()->error->message);
        }
    }

    protected function guardNoContentWithBody(ResponseContract $response): void
    {
        if ($response->status() === ResponseStatusCode::HTTP_NO_CONTENT && ! empty($response->body())) {
            throw new ApiException("No response body found.");
        }
    }

    protected function guardJsonNotDecodable(ResponseContract $response): void
    {
        $response->json();

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ApiException("Unable to decode Mollie response: '{$response->body()}'.");
        }
    }

    protected function throwExceptionIfRequestFailed(ResponseContract $response, ?string $requestBody): void
    {
        if ($this->requestSucceeded($response->status())) {
            return;
        }

        $body = $response->json();

        $message = "Error executing API call ({$body->status}: {$body->title}): {$body->detail}";

        $field = null;

        if (! empty($body->field)) {
            $field = $body->field;
        }

        if (isset($body->_links, $body->_links->documentation)) {
            $message .= ". Documentation: {$body->_links->documentation->href}";
        }

        if ($requestBody) {
            $message .= ". Request body: {$body}";
        }

        throw new ApiException($message, $response->status(), $field);
    }

    /**
     * Determine if the response indicates a client or server error occurred.
     *
     * @return bool
     */
    private function requestSucceeded(int $status)
    {
        return $status < ResponseStatusCode::HTTP_BAD_REQUEST;
    }
}

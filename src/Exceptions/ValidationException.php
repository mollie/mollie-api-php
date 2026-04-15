<?php

declare(strict_types=1);

namespace Mollie\Api\Exceptions;

use Mollie\Api\Http\Response;
use Mollie\Api\Http\ResponseStatusCode;
use Throwable;

class ValidationException extends ApiException
{
    /**
     * @param  array<string, string>  $errors  Map of field name to error message.
     */
    public function __construct(
        Response $response,
        public readonly string $field,
        string $message,
        public readonly array $errors = [],
        int $code = ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY,
        ?Throwable $previous = null
    ) {
        parent::__construct($response, $message, $code, $previous);
    }

    public function getField(): string
    {
        return $this->field;
    }

    /**
     * Get the full field -> message map.
     *
     * @return array<string, string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasError(string $field): bool
    {
        return array_key_exists($field, $this->errors);
    }

    public function getError(string $field): ?string
    {
        return $this->errors[$field] ?? null;
    }

    public static function fromResponse(Response $response): self
    {
        $body = $response->json();
        $field = $body->field ?? '';
        $detail = $body->detail ?? '';

        return new self(
            $response,
            $field,
            'We could not process your request due to validation errors. '.
                sprintf('Error executing API call (%d: %s): %s', 422, $body->title ?? 'Unknown', $detail),
            self::extractErrors($body, $field, $detail),
            ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /**
     * Build a field => message map from the Mollie error body.
     *
     * Mollie's standard 422 body has a single top-level `field` plus `detail`.
     * Some error responses additionally expose per-field errors under `details`,
     * `errors`, or `extra.errors` (shape: field -> message, or a list of
     * { field, message } objects). We accept all those forms.
     *
     * @return array<string, string>
     */
    private static function extractErrors(\stdClass $body, string $field, string $detail): array
    {
        $errors = [];

        foreach (['details', 'errors'] as $key) {
            if (isset($body->{$key})) {
                $errors = array_merge($errors, self::normalizeErrorBag($body->{$key}));
            }
        }

        if (isset($body->extra, $body->extra->errors)) {
            $errors = array_merge($errors, self::normalizeErrorBag($body->extra->errors));
        }

        if ($field !== '' && ! array_key_exists($field, $errors)) {
            $errors[$field] = $detail;
        }

        return $errors;
    }

    /**
     * Normalize an error bag into a field => message map.
     *
     * Accepts:
     *  - stdClass { field: message }
     *  - array<string, string>
     *  - list of stdClass { field, message|detail }
     *
     * @param  mixed  $bag
     * @return array<string, string>
     */
    private static function normalizeErrorBag($bag): array
    {
        $errors = [];

        if ($bag instanceof \stdClass) {
            foreach ((array) $bag as $key => $value) {
                if (is_string($value)) {
                    $errors[$key] = $value;
                }
            }

            return $errors;
        }

        if (! is_array($bag)) {
            return $errors;
        }

        foreach ($bag as $key => $value) {
            if (is_string($key) && is_string($value)) {
                $errors[$key] = $value;

                continue;
            }

            if ($value instanceof \stdClass) {
                $field = $value->field ?? null;
                $message = $value->message ?? $value->detail ?? null;

                if (is_string($field) && is_string($message)) {
                    $errors[$field] = $message;
                }
            }
        }

        return $errors;
    }
}

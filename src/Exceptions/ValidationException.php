<?php

declare(strict_types=1);

namespace Mollie\Api\Exceptions;

use Mollie\Api\Http\Response;
use Mollie\Api\Http\ResponseStatusCode;
use stdClass;
use Throwable;

class ValidationException extends ApiException
{
    /**
     * @param  Response  $response
     * @param  string  $field
     * @param  string  $message
     * @param  array<string, string>  $errors  Map of field name to error message.
     * @param  int  $code
     * @param  Throwable|null  $previous
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
    private static function extractErrors(stdClass $body, string $field, string $detail): array
    {
        $errors = array_merge(...array_map(
            self::normalizeErrorBag(...),
            self::errorBags($body)
        ));

        if ($field !== '' && ! array_key_exists($field, $errors)) {
            $errors[$field] = $detail;
        }

        return $errors;
    }

    /**
     * @return array<int, mixed>
     */
    private static function errorBags(stdClass $body): array
    {
        $bags = [];

        foreach (['details', 'errors'] as $key) {
            if (isset($body->{$key})) {
                $bags[] = $body->{$key};
            }
        }

        if (isset($body->extra, $body->extra->errors)) {
            $bags[] = $body->extra->errors;
        }

        return $bags;
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
        if ($bag instanceof stdClass) {
            return self::normalizeErrorEntries((array) $bag);
        }

        return is_array($bag)
            ? self::normalizeErrorEntries($bag)
            : [];
    }

    /**
     * @param  array<array-key, mixed>  $entries
     * @return array<string, string>
     */
    private static function normalizeErrorEntries(array $entries): array
    {
        $errors = [];

        foreach ($entries as $key => $value) {
            $error = self::normalizeErrorEntry($key, $value);

            if ($error !== null) {
                [$field, $message] = $error;
                $errors[$field] = $message;
            }
        }

        return $errors;
    }

    /**
     * @param  array-key  $key
     * @param  mixed  $value
     * @return array{0: string, 1: string}|null
     */
    private static function normalizeErrorEntry($key, mixed $value): ?array
    {
        if (is_string($key) && is_string($value)) {
            return [$key, $value];
        }

        if (! $value instanceof stdClass) {
            return null;
        }

        $field = $value->field ?? null;
        $message = $value->message ?? $value->detail ?? null;

        return is_string($field) && is_string($message)
            ? [$field, $message]
            : null;
    }
}

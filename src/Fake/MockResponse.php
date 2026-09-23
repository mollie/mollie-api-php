<?php

declare(strict_types=1);

namespace Mollie\Api\Fake;

use Mollie\Api\Fake\Concerns\CreatesResourceResponses;
use Mollie\Api\Traits\HasDefaultFactories;
use Psr\Http\Message\ResponseInterface;
use UnexpectedValueException;

class MockResponse
{
    use CreatesResourceResponses;
    use HasDefaultFactories;

    private const SERIALIZATION_VERSION = 2;

    protected int $status;

    protected string $resourceKey;

    protected string $body;

    /**
     * @param  string|array|callable  $body
     * @param  int  $status
     * @param  string  $resourceKey
     */
    public function __construct(
        $body,
        int $status = 200,
        string $resourceKey = ''
    ) {
        $this->body = $this->convertToJson($body);
        $this->status = $status;
        $this->resourceKey = $resourceKey;
    }

    /**
     * @param  string|array|callable  $body
     */
    private function convertToJson($body): string
    {
        if (is_array($body) && empty($body)) {
            return '{}';
        }

        return is_array($body)
            ? json_encode($body)
            : $body;
    }

    /**
     * @param  string|array  $body
     * @param  string  $resourceKey
     */
    public static function ok($body = [], string $resourceKey = ''): self
    {
        return new self($body, 200, $resourceKey);
    }

    /**
     * @param  string|array  $body
     * @param  string  $resourceKey
     */
    public static function created($body = [], string $resourceKey = ''): self
    {
        return new self($body, 201, $resourceKey);
    }

    public static function noContent(string $resourceKey = ''): self
    {
        return new self('', 204, $resourceKey);
    }

    public static function notFound(string $description = 'No resource found'): self
    {
        return static::error(404, 'Not Found', $description);
    }

    public static function unprocessableEntity(string $description = 'The request cannot be processed.', string $field = 'test'): self
    {
        return static::error(422, 'Unprocessable Entity', $description, $field);
    }

    public static function error(int $status, string $title, string $detail, ?string $field = null): self
    {
        return (new ErrorResponseBuilder($status, $title, $detail, $field))->create();
    }

    public static function list(string $resourceKey): ListResponseBuilder
    {
        return new ListResponseBuilder($resourceKey);
    }

    public static function resource(string $resourceKey): ResourceResponseBuilder
    {
        return new ResourceResponseBuilder($resourceKey);
    }

    public function createPsrResponse(): ResponseInterface
    {
        $psrResponse = $this
            ->factories()
            ->responseFactory
            ->createResponse($this->status);

        $body = $this
            ->factories()
            ->streamFactory
            ->createStream($this->body());

        return $psrResponse->withBody($body);
    }

    public function body(): string
    {
        if (empty($body = $this->body)) {
            return '';
        }

        if ($this->isJson($body)) {
            return $body;
        }

        $contents = json_decode(
            FakeResponseLoader::load($body),
            flags: JSON_THROW_ON_ERROR
        );

        if ($this->resourceKey !== '') {
            $contents = $this->replaceResourceId($contents, $this->resourceKey);
        }

        return json_encode($contents, JSON_THROW_ON_ERROR);
    }

    public function json(): array
    {
        return json_decode($this->body(), true);
    }

    private function isJson($string): bool
    {
        json_decode($string);

        return json_last_error() == JSON_ERROR_NONE;
    }

    private function replaceResourceId(mixed $value, string $resourceId): mixed
    {
        if (is_string($value)) {
            return str_replace('{{ RESOURCE_ID }}', $resourceId, $value);
        }

        if (is_object($value)) {
            $replaced = new \stdClass;

            foreach (get_object_vars($value) as $key => $item) {
                $key = str_replace('{{ RESOURCE_ID }}', $resourceId, (string) $key);
                $replaced->{$key} = $this->replaceResourceId($item, $resourceId);
            }

            return $replaced;
        }

        if (! is_array($value)) {
            return $value;
        }

        $replaced = [];

        foreach ($value as $key => $item) {
            $key = is_string($key)
                ? str_replace('{{ RESOURCE_ID }}', $resourceId, $key)
                : $key;

            $replaced[$key] = $this->replaceResourceId($item, $resourceId);
        }

        return $replaced;
    }

    public function __serialize(): array
    {
        return [
            'version' => self::SERIALIZATION_VERSION,
            'body' => $this->body,
            'status' => $this->status,
            'resourceKey' => $this->resourceKey,
        ];
    }

    public function __unserialize(array $data): void
    {
        $keys = ['body', 'status', 'resourceKey'];

        if (array_key_exists('version', $data)) {
            if ($data['version'] !== self::SERIALIZATION_VERSION) {
                throw new UnexpectedValueException('Unsupported MockResponse serialization version.');
            }

            $keys = ['version', ...$keys];
        }

        if (count($data) !== count($keys) || array_diff($keys, array_keys($data)) !== []) {
            throw new UnexpectedValueException('Invalid MockResponse serialized data shape.');
        }

        if (! is_string($data['body']) || ! is_int($data['status']) || ! is_string($data['resourceKey'])) {
            throw new UnexpectedValueException('Invalid MockResponse serialized data types.');
        }

        $this->body = $data['body'];
        $this->status = $data['status'];
        $this->resourceKey = $data['resourceKey'];
    }
}

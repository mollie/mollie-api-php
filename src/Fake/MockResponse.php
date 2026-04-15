<?php

declare(strict_types=1);

namespace Mollie\Api\Fake;

use BackedEnum;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Traits\HasDefaultFactories;
use Psr\Http\Message\ResponseInterface;

class MockResponse
{
    use HasDefaultFactories;

    protected int $status;

    protected string $resourceKey;

    protected string $body;

    /**
     * @param  string|array|callable  $body
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
     */
    public static function ok($body = [], string $resourceKey = ''): self
    {
        return new self($body, 200, $resourceKey);
    }

    /**
     * @param  string|array  $body
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

    /**
     * Build a payment response — the base fixture provides defaults,
     * any named override replaces the matching top-level field.
     *
     * @param  \BackedEnum|string|null  $status
     */
    public static function payment(
        ?string $id = null,
        $status = null,
        ?Money $amount = null,
        ?string $description = null,
        ?string $method = null,
        array $overrides = [],
    ): self {
        return self::fromFixture('payment', self::mergeOverrides([
            'id' => $id ?? self::generateId('tr_'),
            'status' => self::enumValue($status),
            'amount' => self::moneyToArray($amount),
            'description' => $description,
            'method' => $method,
        ], $overrides));
    }

    public static function customer(
        ?string $id = null,
        ?string $name = null,
        ?string $email = null,
        ?string $locale = null,
        array $overrides = [],
    ): self {
        return self::fromFixture('customer', self::mergeOverrides([
            'id' => $id ?? self::generateId('cst_'),
            'name' => $name,
            'email' => $email,
            'locale' => $locale,
        ], $overrides));
    }

    /**
     * @param  \BackedEnum|string|null  $status
     */
    public static function subscription(
        ?string $id = null,
        $status = null,
        ?Money $amount = null,
        ?string $description = null,
        ?string $customerId = null,
        array $overrides = [],
    ): self {
        return self::fromFixture('subscription', self::mergeOverrides([
            'id' => $id ?? self::generateId('sub_'),
            'status' => self::enumValue($status),
            'amount' => self::moneyToArray($amount),
            'description' => $description,
            'customerId' => $customerId,
        ], $overrides));
    }

    /**
     * @param  \BackedEnum|string|null  $status
     */
    public static function mandate(
        ?string $id = null,
        $status = null,
        ?string $method = null,
        ?string $customerId = null,
        array $overrides = [],
    ): self {
        return self::fromFixture('mandate', self::mergeOverrides([
            'id' => $id ?? self::generateId('mdt_'),
            'status' => self::enumValue($status),
            'method' => $method,
            'customerId' => $customerId,
        ], $overrides));
    }

    /**
     * @param  \BackedEnum|string|null  $status
     */
    public static function refund(
        ?string $id = null,
        $status = null,
        ?Money $amount = null,
        ?string $description = null,
        ?string $paymentId = null,
        array $overrides = [],
    ): self {
        return self::fromFixture('refund', self::mergeOverrides([
            'id' => $id ?? self::generateId('re_'),
            'status' => self::enumValue($status),
            'amount' => self::moneyToArray($amount),
            'description' => $description,
            'paymentId' => $paymentId,
        ], $overrides));
    }

    public static function chargeback(
        ?string $id = null,
        ?Money $amount = null,
        ?string $paymentId = null,
        array $overrides = [],
    ): self {
        return self::fromFixture('chargeback', self::mergeOverrides([
            'id' => $id ?? self::generateId('chb_'),
            'amount' => self::moneyToArray($amount),
            'paymentId' => $paymentId,
        ], $overrides));
    }

    /**
     * @param  \BackedEnum|string|null  $status
     */
    public static function method(
        ?string $id = null,
        ?string $description = null,
        $status = null,
        array $overrides = [],
    ): self {
        return self::fromFixture('method', self::mergeOverrides([
            'id' => $id,
            'description' => $description,
            'status' => self::enumValue($status),
        ], $overrides));
    }

    /**
     * @param  \BackedEnum|string|null  $status
     */
    public static function paymentLink(
        ?string $id = null,
        ?string $description = null,
        ?Money $amount = null,
        $status = null,
        array $overrides = [],
    ): self {
        return self::fromFixture('payment-link', self::mergeOverrides([
            'id' => $id ?? self::generateId('pl_'),
            'description' => $description,
            'amount' => self::moneyToArray($amount),
            'status' => self::enumValue($status),
        ], $overrides));
    }

    /**
     * @param  \BackedEnum|string|null  $status
     */
    public static function invoice(
        ?string $id = null,
        ?string $reference = null,
        $status = null,
        ?Money $grossAmount = null,
        array $overrides = [],
    ): self {
        return self::fromFixture('invoice', self::mergeOverrides([
            'id' => $id ?? self::generateId('inv_'),
            'reference' => $reference,
            'status' => self::enumValue($status),
            'grossAmount' => self::moneyToArray($grossAmount),
        ], $overrides));
    }

    public static function capture(
        ?string $id = null,
        ?Money $amount = null,
        ?string $description = null,
        ?string $paymentId = null,
        array $overrides = [],
    ): self {
        return self::fromFixture('capture', self::mergeOverrides([
            'id' => $id ?? self::generateId('cpt_'),
            'amount' => self::moneyToArray($amount),
            'description' => $description,
            'paymentId' => $paymentId,
        ], $overrides));
    }

    /**
     * Build a MockResponse by loading the given fixture, replacing the
     * `{{ RESOURCE_ID }}` placeholder, and overlaying the given overrides.
     *
     * @param  array<string, mixed>  $overrides  Fields to replace at the top level.
     */
    private static function fromFixture(string $fixtureKey, array $overrides): self
    {
        /** @var array<string, mixed> $base */
        $base = json_decode(FakeResponseLoader::load($fixtureKey), true);

        $id = isset($overrides['id']) && is_string($overrides['id'])
            ? $overrides['id']
            : '';

        // Swap the `{{ RESOURCE_ID }}` placeholder for the concrete id when one is provided.
        if ($id !== '' && isset($base['id']) && $base['id'] === '{{ RESOURCE_ID }}') {
            $base['id'] = $id;
        }

        $merged = array_replace($base, $overrides);

        return new self($merged, 200, $id);
    }

    /**
     * Merge the factory's typed args with the free-form overrides, dropping null values.
     *
     * @param  array<string, mixed>  $typed  Map of field -> value from named args (null == unset).
     * @param  array<string, mixed>  $extra  Free-form overrides (take precedence).
     * @return array<string, mixed>
     */
    private static function mergeOverrides(array $typed, array $extra): array
    {
        $filtered = [];

        foreach ($typed as $key => $value) {
            if ($value !== null) {
                $filtered[$key] = $value;
            }
        }

        return array_replace($filtered, $extra);
    }

    /**
     * @param  \BackedEnum|string|null  $value
     */
    private static function enumValue($value): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof BackedEnum) {
            return (string) $value->value;
        }

        return $value;
    }

    /**
     * @return array<string, string>|null
     */
    private static function moneyToArray(?Money $money): ?array
    {
        return $money?->toArray();
    }

    private static function generateId(string $prefix): string
    {
        return $prefix.bin2hex(random_bytes(5));
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

        /** @var string $contents */
        $contents = FakeResponseLoader::load($body);

        if (! empty($this->resourceKey)) {
            $contents = str_replace('{{ RESOURCE_ID }}', $this->resourceKey, $contents);
        }

        return $contents;
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

    public function __serialize(): array
    {
        return [
            'body' => $this->body(),
            'status' => $this->json()['status'] ?? 200,
            'resourceKey' => $this->json()['resource_key'] ?? '',
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->body = $data['body'];
        $this->status = $data['status'];
        $this->resourceKey = $data['resourceKey'];
    }
}

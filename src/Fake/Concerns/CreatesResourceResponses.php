<?php

declare(strict_types=1);

namespace Mollie\Api\Fake\Concerns;

use BackedEnum;
use Mollie\Api\Fake\FakeResponseLoader;
use Mollie\Api\Http\Data\Money;

trait CreatesResourceResponses
{
    /**
     * Build a payment response. The base fixture provides defaults; named
     * overrides replace the matching top-level field.
     *
     * @param  string|null  $id
     * @param  BackedEnum|string|null  $status
     * @param  Money|null  $amount
     * @param  string|null  $description
     * @param  string|null  $method
     * @param  array<string, mixed>  $overrides
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
     * @param  string|null  $id
     * @param  BackedEnum|string|null  $status
     * @param  Money|null  $amount
     * @param  string|null  $description
     * @param  string|null  $customerId
     * @param  array<string, mixed>  $overrides
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
     * @param  string|null  $id
     * @param  BackedEnum|string|null  $status
     * @param  string|null  $method
     * @param  string|null  $customerId
     * @param  array<string, mixed>  $overrides
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
     * @param  string|null  $id
     * @param  BackedEnum|string|null  $status
     * @param  Money|null  $amount
     * @param  string|null  $description
     * @param  string|null  $paymentId
     * @param  array<string, mixed>  $overrides
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
     * @param  string|null  $id
     * @param  string|null  $description
     * @param  BackedEnum|string|null  $status
     * @param  array<string, mixed>  $overrides
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
     * @param  string|null  $id
     * @param  string|null  $description
     * @param  Money|null  $amount
     * @param  BackedEnum|string|null  $status
     * @param  array<string, mixed>  $overrides
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
     * @param  string|null  $id
     * @param  string|null  $reference
     * @param  BackedEnum|string|null  $status
     * @param  Money|null  $grossAmount
     * @param  array<string, mixed>  $overrides
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
     * @param  string  $fixtureKey
     * @param  array<string, mixed>  $overrides
     */
    private static function fromFixture(string $fixtureKey, array $overrides): self
    {
        /** @var array<string, mixed> $base */
        $base = json_decode(FakeResponseLoader::load($fixtureKey), true);

        $id = isset($overrides['id']) && is_string($overrides['id'])
            ? $overrides['id']
            : '';

        if ($id !== '' && isset($base['id']) && $base['id'] === '{{ RESOURCE_ID }}') {
            $base['id'] = $id;
        }

        return new self(array_replace($base, $overrides), 200, $id);
    }

    /**
     * @param  array<string, mixed>  $typed
     * @param  array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    private static function mergeOverrides(array $typed, array $extra): array
    {
        return array_replace(
            array_filter($typed, static fn ($value): bool => $value !== null),
            $extra
        );
    }

    /**
     * @param  BackedEnum|string|null  $value
     */
    private static function enumValue($value): ?string
    {
        if ($value instanceof BackedEnum) {
            return (string) $value->value;
        }

        return is_string($value) ? $value : null;
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
}

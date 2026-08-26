<?php

declare(strict_types=1);

namespace Tests\Resources;

use BackedEnum;
use Mollie\Api\Http\Response;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\BaseResource;
use Mollie\Api\Resources\CurrentProfile;
use Mollie\Api\Resources\Invoice;
use Mollie\Api\Resources\Mandate;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\Profile;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Resources\ResourceHydrator;
use Mollie\Api\Resources\Settlement;
use Mollie\Api\Resources\Subscription;
use Mollie\Api\Types\InvoiceStatus;
use Mollie\Api\Types\MandateStatus;
use Mollie\Api\Types\PaymentMethod;
use Mollie\Api\Types\ProfileStatus;
use Mollie\Api\Types\RefundStatus;
use Mollie\Api\Types\SequenceType;
use Mollie\Api\Types\SettlementStatus;
use Mollie\Api\Types\SubscriptionStatus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Every shipped `Enum|string|null` property must hydrate known values to the
 * enum case (identity, not Utility::equals), keep unknown strings, and keep null.
 */
class NullableEnumUnionHydrationTest extends TestCase
{
    /**
     * @param  class-string<BaseResource>  $resourceClass
     * @param  class-string<BackedEnum>  $enumClass
     */
    #[DataProvider('dpNullableEnumUnionProperties')]
    #[Test]
    public function it_hydrates_nullable_enum_unions(string $resourceClass, string $property, string $enumClass): void
    {
        $case = $enumClass::cases()[0];

        $known = $this->hydrate($resourceClass, [$property => $case->value]);
        $unknown = $this->hydrate($resourceClass, [$property => 'value-from-the-future']);
        $null = $this->hydrate($resourceClass, [$property => null]);

        $this->assertSame($case, $known->{$property});
        $this->assertSame('value-from-the-future', $unknown->{$property});
        $this->assertNull($null->{$property});
    }

    public static function dpNullableEnumUnionProperties(): array
    {
        return [
            'Payment::$method' => [Payment::class, 'method', PaymentMethod::class],
            'Payment::$sequenceType' => [Payment::class, 'sequenceType', SequenceType::class],
            'Refund::$status' => [Refund::class, 'status', RefundStatus::class],
            'Mandate::$status' => [Mandate::class, 'status', MandateStatus::class],
            'Settlement::$status' => [Settlement::class, 'status', SettlementStatus::class],
            'Profile::$status' => [Profile::class, 'status', ProfileStatus::class],
            'CurrentProfile::$status' => [CurrentProfile::class, 'status', ProfileStatus::class],
            'Invoice::$status' => [Invoice::class, 'status', InvoiceStatus::class],
            'Subscription::$status' => [Subscription::class, 'status', SubscriptionStatus::class],
        ];
    }

    #[Test]
    public function profile_category_code_keeps_integer_and_string_values(): void
    {
        $this->assertSame(5499, $this->hydrate(Profile::class, ['categoryCode' => 5499])->categoryCode);
        $this->assertSame('5499', $this->hydrate(Profile::class, ['categoryCode' => '5499'])->categoryCode);
        $this->assertNull($this->hydrate(Profile::class, ['categoryCode' => null])->categoryCode);
    }

    /**
     * Hydrate into a typed local and return it: `ResourceHydrator::hydrate()` returns
     * `BaseResource`, which PHPStan level 5 rejects as the return of a `T`-typed helper.
     *
     * @template T of BaseResource
     *
     * @param  class-string<T>  $resourceClass
     * @return T
     */
    private function hydrate(string $resourceClass, array $data): BaseResource
    {
        $resource = new $resourceClass($this->createMock(MollieApiClient::class));
        (new ResourceHydrator)->hydrate($resource, $data, $this->createMock(Response::class));

        return $resource;
    }
}

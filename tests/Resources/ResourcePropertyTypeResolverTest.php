<?php

declare(strict_types=1);

namespace Tests\Resources;

use Mollie\Api\Http\Data\Money;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\BaseResource;
use Mollie\Api\Resources\ResourcePropertyTypeResolver;
use Mollie\Api\Types\PaymentStatus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ResourcePropertyTypeResolverTest extends TestCase
{
    private ResourcePropertyTypeResolver $resolver;

    private array $types;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver = new ResourcePropertyTypeResolver;
        $this->types = $this->resolver->typesFor(new DeclarationShapesResource($this->createMock(MollieApiClient::class)));
    }

    #[DataProvider('dpDeclarationKinds')]
    #[Test]
    public function it_classifies_each_declaration_shape(string $property, string $kind, ?string $scalar): void
    {
        $this->assertSame($kind, $this->types[$property]['kind']);
        $this->assertSame($scalar, $this->types[$property]['scalar'] ?? null);
    }

    public static function dpDeclarationKinds(): array
    {
        return [
            'Enum|string' => ['enumOrString', 'enum', null],
            'Enum|string|null' => ['nullableEnumOrString', 'enum', null],
            '?Enum' => ['nullableEnum', 'enum', null],
            '?Money' => ['nullableMoney', 'valueObject', null],
            'int|string|null' => ['nullableIntOrString', 'mixed', null],
            '?string' => ['nullableString', 'scalar', 'string'],
            '?int' => ['nullableInt', 'scalar', 'int'],
            'string|int' => ['stringOrInt', 'mixed', null],
        ];
    }

    #[Test]
    public function nullable_enum_union_casts_known_values_and_keeps_unknown_and_null(): void
    {
        $descriptor = $this->types['nullableEnumOrString'];

        $this->assertSame(PaymentStatus::Paid, $this->resolver->cast($descriptor, 'paid'));
        $this->assertSame('from-the-future', $this->resolver->cast($descriptor, 'from-the-future'));
        $this->assertNull($this->resolver->cast($descriptor, null));
    }

    #[Test]
    public function named_nullable_types_keep_their_existing_casting(): void
    {
        $this->assertSame(PaymentStatus::Paid, $this->resolver->cast($this->types['nullableEnum'], 'paid'));

        $money = $this->resolver->cast($this->types['nullableMoney'], ['currency' => 'EUR', 'value' => '1.00']);
        $this->assertInstanceOf(Money::class, $money);
        $this->assertSame('1.00', $money->value);
    }

    #[Test]
    public function heterogeneous_scalar_unions_are_not_coerced(): void
    {
        $descriptor = $this->types['nullableIntOrString'];

        $this->assertSame(5, $this->resolver->cast($descriptor, 5));
        $this->assertSame('5', $this->resolver->cast($descriptor, '5'));
        $this->assertNull($this->resolver->cast($descriptor, null));
    }

    #[Test]
    public function single_scalar_unions_still_coerce(): void
    {
        $this->assertSame('12', $this->resolver->cast($this->types['nullableString'], 12));
        $this->assertSame(12, $this->resolver->cast($this->types['nullableInt'], '12'));
    }
}

/**
 * One property per declaration shape the hydrator must understand.
 */
class DeclarationShapesResource extends BaseResource
{
    public PaymentStatus|string $enumOrString;

    public PaymentStatus|string|null $nullableEnumOrString = null;

    public ?PaymentStatus $nullableEnum = null;

    public ?Money $nullableMoney = null;

    public int|string|null $nullableIntOrString = null;

    public ?string $nullableString = null;

    public ?int $nullableInt = null;

    public string|int $stringOrInt;
}

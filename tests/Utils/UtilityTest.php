<?php

namespace Tests\Utils;

use Mollie\Api\Http\Data\Money;
use Mollie\Api\Utils\Utility;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class UtilityTest extends TestCase
{
    /** @test */
    public function class_uses_recursive()
    {
        $result = Utility::classUsesRecursive(new TestChildClass);

        $this->assertContains(TestTrait1::class, $result);
        $this->assertContains(TestTrait2::class, $result);
        $this->assertContains(TestTrait3::class, $result);
    }

    /** @test */
    public function trait_uses_recursive()
    {
        $result = Utility::traitUsesRecursive(TestTraitMain::class);

        $this->assertContains(TestTraitBase::class, $result);
        $this->assertContains(TestTraitNested::class, $result);
    }

    /** @test */
    public function get_properties()
    {
        // Test getting all properties
        $allProps = Utility::getProperties(TestPropertiesClass::class);
        $this->assertCount(3, $allProps);
        $this->assertContainsOnlyInstancesOf(ReflectionProperty::class, $allProps);

        // Test getting only public properties
        $publicProps = Utility::getProperties(TestPropertiesClass::class, ReflectionProperty::IS_PUBLIC);
        $this->assertCount(1, $publicProps);
        $this->assertEquals('publicProp', $publicProps[0]->getName());
    }

    /** @test */
    public function filter_by_properties()
    {
        $array = [
            'prop1' => 'value1',
            'prop2' => 'value2',
            'extraProp' => 'extraValue',
        ];

        $filtered = Utility::filterByProperties(TestFilterClass::class, $array);

        $this->assertArrayHasKey('extraProp', $filtered);
        $this->assertArrayNotHasKey('prop1', $filtered);
        $this->assertArrayNotHasKey('prop2', $filtered);
    }

    /** @test */
    public function transform()
    {
        // Test with primitive value and callable resolver
        $transformedWithCallable = Utility::transform(5, fn ($x) => $x * 2);
        $this->assertEquals(10, $transformedWithCallable);

        // Test with primitive value and class resolver
        $transformedWithClass = Utility::transform('test', TestComposable::class);
        $this->assertInstanceOf(TestComposable::class, $transformedWithClass);
        $this->assertEquals('test', $transformedWithClass->value);

        // Test with falsy value
        $transformedWithDefault = Utility::transform(false, fn ($x) => $x * 2, 'default');
        $this->assertEquals('default', $transformedWithDefault);

        // Test with matching instance - should return as is
        $money = new Money('EUR', '100');
        $existingValueIsNotOverriden = Utility::transform($money, Money::class);
        $this->assertSame($money, $existingValueIsNotOverriden);
        $this->assertEquals(['currency' => 'EUR', 'value' => '100'], $existingValueIsNotOverriden->toArray());

        // Test when third argument is a non-existent class (should be treated as default)
        $nonExistentClass = Utility::transform(false, fn ($x) => $x * 2, 'NonExistentClass');
        $this->assertEquals('NonExistentClass', $nonExistentClass);

        // Test with null value and default
        $nullWithDefault = Utility::transform(null, TestComposable::class, 'default_value');
        $this->assertEquals('default_value', $nullWithDefault);

        // Test with empty string value
        $emptyStringValue = Utility::transform('', TestComposable::class, 'default_value');
        $this->assertEquals('default_value', $emptyStringValue);

        // Test with zero value (should be considered falsy)
        $zeroValue = Utility::transform(0, TestComposable::class, 'default_value');
        $this->assertEquals('default_value', $zeroValue);
    }

    /** @test */
    public function extract_bool()
    {
        // Test with direct boolean
        $this->assertTrue(Utility::extractBool(true, 'key'));
        $this->assertFalse(Utility::extractBool(false, 'key'));

        // Test with array
        $array = ['enabled' => true];
        $this->assertTrue(Utility::extractBool($array, 'enabled'));
        $this->assertFalse(Utility::extractBool($array, 'nonexistent'));

        // Test with default value
        $this->assertTrue(Utility::extractBool([], 'key', true));
    }
}

trait TestTrait1
{
}
trait TestTrait2
{
}
trait TestTrait3
{
    use TestTrait1;
}

class TestParentClass
{
    use TestTrait1;
}

class TestChildClass extends TestParentClass
{
    use TestTrait2;
    use TestTrait3;
}

trait TestTraitBase
{
}
trait TestTraitNested
{
    use TestTraitBase;
}
trait TestTraitMain
{
    use TestTraitNested;
}

class TestPropertiesClass
{
    public $publicProp;

    protected $protectedProp;

    private $privateProp;
}

class TestFilterClass
{
    public $prop1;

    public $prop2;
}

class TestComposable
{
    public $value;

    public function __construct($value)
    {
        $this->value = $value;
    }
}

class TestSourceClass
{
    public $value;

    public function __construct($value)
    {
        $this->value = $value;
    }
}

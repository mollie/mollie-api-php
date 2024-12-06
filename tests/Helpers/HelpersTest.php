<?php

namespace Tests\Helpers;

use Mollie\Api\Helpers;
use Mollie\Api\Http\Payload\Metadata;
use Tests\TestCase;
use ReflectionProperty;

class HelpersTest extends TestCase
{
    /** @test */
    public function class_uses_recursive()
    {
        $result = Helpers::classUsesRecursive(new TestChildClass());

        $this->assertContains(TestTrait1::class, $result);
        $this->assertContains(TestTrait2::class, $result);
        $this->assertContains(TestTrait3::class, $result);
    }

    /** @test */
    public function trait_uses_recursive()
    {
        $result = Helpers::traitUsesRecursive(TestTraitMain::class);

        $this->assertContains(TestTraitBase::class, $result);
        $this->assertContains(TestTraitNested::class, $result);
    }

    /** @test */
    public function get_properties()
    {
        // Test getting all properties
        $allProps = Helpers::getProperties(TestPropertiesClass::class);
        $this->assertCount(3, $allProps);
        $this->assertContainsOnlyInstancesOf(ReflectionProperty::class, $allProps);

        // Test getting only public properties
        $publicProps = Helpers::getProperties(TestPropertiesClass::class, ReflectionProperty::IS_PUBLIC);
        $this->assertCount(1, $publicProps);
        $this->assertEquals('publicProp', $publicProps[0]->getName());
    }

    /** @test */
    public function filter_by_properties()
    {
        $array = [
            'prop1' => 'value1',
            'prop2' => 'value2',
            'extraProp' => 'extraValue'
        ];

        $filtered = Helpers::filterByProperties(TestFilterClass::class, $array);

        $this->assertArrayHasKey('extraProp', $filtered);
        $this->assertArrayNotHasKey('prop1', $filtered);
        $this->assertArrayNotHasKey('prop2', $filtered);
    }

    /** @test */
    public function compose()
    {
        // Test with callable
        $composedWithCallable = Helpers::compose(5, fn($x) => $x * 2);
        $this->assertEquals(10, $composedWithCallable);

        $composedWithClass = Helpers::compose('test', TestComposable::class);
        $this->assertInstanceOf(TestComposable::class, $composedWithClass);
        $this->assertEquals('test', $composedWithClass->value);

        // Test with falsy value
        $composedWithDefault = Helpers::compose(false, fn($x) => $x * 2, 'default');
        $this->assertEquals('default', $composedWithDefault);

        $existingValueIsNotOverriden = Helpers::compose(new Metadata(['key' => 'value']), Metadata::class);
        $this->assertInstanceOf(Metadata::class, $existingValueIsNotOverriden);
        $this->assertEquals(['key' => 'value'], $existingValueIsNotOverriden->data);
    }

    /** @test */
    public function extract_bool()
    {
        // Test with direct boolean
        $this->assertTrue(Helpers::extractBool(true, 'key'));
        $this->assertFalse(Helpers::extractBool(false, 'key'));

        // Test with array
        $array = ['enabled' => true];
        $this->assertTrue(Helpers::extractBool($array, 'enabled'));
        $this->assertFalse(Helpers::extractBool($array, 'nonexistent'));

        // Test with default value
        $this->assertTrue(Helpers::extractBool([], 'key', true));
    }
}

trait TestTrait1 {}
trait TestTrait2 {}
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
    use TestTrait2, TestTrait3;
}

trait TestTraitBase {}
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
    public function __construct(public $value) {}
}

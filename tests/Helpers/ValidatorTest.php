<?php

namespace Tests\Helpers;

use Mollie\Api\Contracts\ValidatableDataProvider;
use Mollie\Api\Exceptions\RequestValidationException;
use Mollie\Api\Helpers\Validator;
use Mollie\Api\Http\Request;
use Mollie\Api\Rules\Id;
use Mollie\Api\Rules\Included;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    /** @test */
    public function it_validates_all_properties_with_matching_rules()
    {
        $this->expectException(RequestValidationException::class);
        $this->expectExceptionMessage("Invalid foo ID: 'bar_123'. A resource ID should start with 'foo'.");

        $foo = new HasProperties('bar_123');
        $validator = new Validator;

        $validator->validate($foo);
    }

    /** @test */
    public function it_validates_all_validatable_properties_by_deferring_to_their_validate_methods()
    {
        $this->expectException(RequestValidationException::class);
        $this->expectExceptionMessage("Invalid include: 'foo'. Allowed are: bar.");

        $foo = new HasValidatableProperties(new ValidatableData('foo'));
        $validator = new Validator;

        $validator->validate($foo);
    }

    /** @test */
    public function it_does_not_validate_null_validatable_properties()
    {
        $foo = new HasValidatableProperties(null); // ValidatableData is null
        $validator = new Validator;

        // No exception should be thrown because the ValidatableData is null
        $validator->validate($foo);

        $this->assertTrue(true); // If no exception is thrown, the test passes
    }

    /** @test */
    public function it_validates_additional_properties_without_overriding_provider_data()
    {
        $this->expectException(RequestValidationException::class);
        $this->expectExceptionMessage("Invalid foo ID: 'bar_123'. A resource ID should start with 'foo'.");

        $foo = new HasProperties('foo_123'); // Valid ID from provider
        $validator = new Validator;

        $additional = ['extra_id' => 'bar_123']; // Invalid additional ID with a different key

        $validator->validate($foo, $additional);
    }
}

class HasProperties extends Request
{
    protected string $id;

    /**
     * The resource class the request should be casted to.
     */
    public static string $targetResourceClass = 'foo';

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function resolveResourcePath(): string
    {
        return 'foo';
    }

    public function rules(): array
    {
        return [
            'id' => Id::startsWithPrefix('foo'),
            'extra_id' => Id::startsWithPrefix('foo'), // New rule for extra_id
        ];
    }
}

class HasValidatableProperties implements ValidatableDataProvider
{
    protected ?ValidatableData $validatableData;

    public function __construct(?ValidatableData $validatableData)
    {
        $this->validatableData = $validatableData;
    }

    public function rules(): array
    {
        return [];
    }
}

class ValidatableData implements ValidatableDataProvider
{
    protected string $included;

    public function __construct(string $included)
    {
        $this->included = $included;
    }

    public function rules(): array
    {
        return [
            'included' => Included::make(['bar']),
        ];
    }
}

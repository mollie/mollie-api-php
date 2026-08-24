<?php

declare(strict_types=1);

namespace Tests\Utils;

use Mollie\Api\Contracts\Resolvable;
use Mollie\Api\Contracts\Stringable;
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Http\Data\Address;
use Mollie\Api\Http\Data\DataCollection;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Data\PaymentRoute;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Requests\DynamicGetRequest;
use Mollie\Api\Http\Requests\DynamicPostRequest;
use Mollie\Api\Utils\DataTransformer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DataTransformerTest extends TestCase
{
    private DataTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new DataTransformer;
    }

    #[Test]
    public function it_transforms_query_parameters(): void
    {
        $pendingRequest = $this->createGetRequest();
        $pendingRequest->query()->add('enabled', true);
        $pendingRequest->query()->add('disabled', false);
        $pendingRequest->query()->add('nested', ['value' => true]);

        $result = $this->transformer->transform($pendingRequest);

        $this->assertEquals('true', $result->query()->get('enabled'));
        $this->assertEquals('false', $result->query()->get('disabled'));
        $this->assertEquals(['value' => 'true'], $result->query()->get('nested'));
    }

    #[Test]
    public function it_transforms_payload_data(): void
    {
        $pendingRequest = $this->createPostRequest();
        $pendingRequest->payload()->add('dateTime', '2024-01-01T11:00:00+00:00');
        $pendingRequest->payload()->add('empty', '');
        $pendingRequest->payload()->add('null', null);
        $pendingRequest->payload()->add('valid', 'data');
        $pendingRequest->payload()->add('address', new Address(
            null,
            'John',
            'Doe',
            null,
            '123 Main St',
            null,
            '0',
            null,
            null,
            'Anytown',
            null,
            'BE'
        ));

        $result = $this->transformer->transform($pendingRequest);

        $this->assertEquals('2024-01-01T11:00:00+00:00', (string) $result->payload()->get('dateTime'));
        $this->assertFalse($result->payload()->has('empty'));
        $this->assertFalse($result->payload()->has('null'));
        $this->assertEquals('data', $result->payload()->get('valid'));
        $this->assertEqualsCanonicalizing([
            'givenName' => 'John',
            'familyName' => 'Doe',
            'streetAndNumber' => '123 Main St',
            'postalCode' => '0',
            'city' => 'Anytown',
            'country' => 'BE',
        ], $result->payload()->get('address'));
    }

    #[Test]
    public function it_resolves_complex_data_structures(): void
    {
        $pendingRequest = $this->createPostRequest();
        $pendingRequest->payload()->add('routes', new DataCollection([
            new PaymentRoute(
                new Money('EUR', '10.00'),
                'org_1234567890',
                null
            ),
        ]));

        $result = $this->transformer->transform($pendingRequest);

        $expected = [
            [
                'amount' => ['currency' => 'EUR', 'value' => '10.00'],
                'destination' => ['type' => 'organization', 'organizationId' => 'org_1234567890'],
            ],
        ];

        $this->assertEquals($expected, $result->payload()->get('routes'));
    }

    #[Test]
    public function it_transforms_stringable_and_resolvable_objects(): void
    {
        $pendingRequest = $this->createPostRequest();
        $pendingRequest->payload()->add('resolvable', new Foo('value', new Bar('nested')));

        $result = $this->transformer->transform($pendingRequest);

        $this->assertEquals(['foo' => 'value', 'bar' => 'nested'], $result->payload()->get('resolvable'));
    }

    #[Test]
    public function it_handles_boolean_values_correctly(): void
    {
        $pendingRequest = $this->createGetRequest();
        $pendingRequest->query()->add('true', true);
        $pendingRequest->query()->add('false', false);
        $pendingRequest->query()->add('string', 'value');
        $pendingRequest->query()->add('number', 123);

        $result = $this->transformer->transform($pendingRequest);

        $this->assertEquals('true', $result->query()->get('true'));
        $this->assertEquals('false', $result->query()->get('false'));
        $this->assertEquals('value', $result->query()->get('string'));
        $this->assertEquals(123, $result->query()->get('number'));
    }

    #[Test]
    public function it_preserves_zero_values_in_payload(): void
    {
        $pendingRequest = $this->createPostRequest();
        $pendingRequest->payload()->add('description', '0');
        $pendingRequest->payload()->add('intZero', 0);
        $pendingRequest->payload()->add('floatZero', 0.0);
        $pendingRequest->payload()->add('empty', '');
        $pendingRequest->payload()->add('emptyArray', []);
        $pendingRequest->payload()->add('null', null);

        $result = $this->transformer->transform($pendingRequest);

        $this->assertSame('0', $result->payload()->get('description'));
        $this->assertSame(0, $result->payload()->get('intZero'));
        $this->assertSame(0.0, $result->payload()->get('floatZero'));
        $this->assertFalse($result->payload()->has('empty'));
        $this->assertFalse($result->payload()->has('emptyArray'));
        $this->assertFalse($result->payload()->has('null'));
    }

    #[Test]
    public function it_preserves_zero_values_in_query(): void
    {
        $pendingRequest = $this->createGetRequest();
        $pendingRequest->query()->add('limit', 0);
        $pendingRequest->query()->add('label', '0');

        $result = $this->transformer->transform($pendingRequest);

        $this->assertSame(0, $result->query()->get('limit'));
        $this->assertSame('0', $result->query()->get('label'));
    }

    private function createGetRequest(): PendingRequest
    {
        return new PendingRequest(
            new MockMollieClient,
            new DynamicGetRequest('')
        );
    }

    private function createPostRequest(): PendingRequest
    {
        return new PendingRequest(
            new MockMollieClient,
            new DynamicPostRequest('')
        );
    }
}

// Using the same test classes from ArrTest
class Foo implements Resolvable
{
    public string $foo;

    public Bar $bar;

    public function __construct(string $foo, Bar $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }

    public function toArray(): array
    {
        return [
            'foo' => $this->foo,
            'bar' => $this->bar,
        ];
    }
}

class Bar implements Stringable
{
    public string $bar;

    public function __construct(string $bar)
    {
        $this->bar = $bar;
    }

    public function __toString(): string
    {
        return $this->bar;
    }
}

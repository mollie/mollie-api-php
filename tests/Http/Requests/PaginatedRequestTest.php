<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Query\Query;
use Mollie\Api\Http\Requests\PaginatedRequest;
use Mollie\Api\Resources\BaseCollection;
use Tests\TestCase;

class PaginatedRequestTest extends TestCase
{
    /** @test */
    public function it_can_handle_null_query()
    {
        $request = new ConcretePaginatedRequest;

        $this->assertEquals([], $request->query()->all());
    }

    /** @test */
    public function it_can_handle_query()
    {
        $query = new ConcreteQuery(['limit' => 10]);
        $request = new ConcretePaginatedRequest($query);

        $this->assertEquals(['limit' => 10], $request->query()->all());
    }
}

class ConcretePaginatedRequest extends PaginatedRequest
{
    public static string $targetResourceClass = BaseCollection::class;

    public function resolveResourcePath(): string
    {
        return 'test';
    }
}

class ConcreteQuery extends Query
{
    private array $parameters;

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function toArray(): array
    {
        return $this->parameters;
    }
}
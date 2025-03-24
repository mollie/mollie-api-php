<?php

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\PaginatedRequest;
use Mollie\Api\Resources\BaseCollection;
use PHPUnit\Framework\TestCase;

class PaginatedRequestTest extends TestCase
{
    /** @test */
    public function it_can_handle_null_query()
    {
        $request = new ConcretePaginatedRequest;

        $this->assertEquals(['from' => null, 'limit' => null], $request->query()->all());
    }

    /** @test */
    public function it_can_handle_query()
    {
        $request = new ConcretePaginatedRequest(null, 10);

        $this->assertEquals(['from' => null, 'limit' => 10], $request->query()->all());
    }
}

class ConcretePaginatedRequest extends PaginatedRequest
{
    protected $hydratableResource = BaseCollection::class;

    public function resolveResourcePath(): string
    {
        return 'test';
    }
}

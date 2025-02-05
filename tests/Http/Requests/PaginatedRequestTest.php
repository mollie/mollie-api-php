<?php

namespace Tests\Http\Requests;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Http\Requests\PaginatedRequest;
use Mollie\Api\Resources\BaseCollection;
use PHPUnit\Framework\TestCase;

class PaginatedRequestTest extends TestCase
{
    /** @test */
    public function it_can_handle_null_query()
    {
        $request = new ConcretePaginatedRequest;

        $this->assertEquals([], $request->query()->resolve()->all());
    }

    /** @test */
    public function it_can_handle_query()
    {
        $request = new ConcretePaginatedRequest(null, 10);

        $this->assertEquals(['limit' => 10], $request->query()->resolve()->all());
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

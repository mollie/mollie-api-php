<?php

declare(strict_types=1);

namespace Tests\Http\Requests;

use Mollie\Api\Http\Requests\PaginatedRequest;
use Mollie\Api\Resources\BaseCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PaginatedRequestTest extends TestCase
{
    #[Test]
    public function it_can_handle_null_query()
    {
        $request = new ConcretePaginatedRequest;

        $this->assertEquals(['from' => null, 'limit' => null], $request->query()->all());
    }

    #[Test]
    public function it_can_handle_query()
    {
        $request = new ConcretePaginatedRequest(null, 10);

        $this->assertEquals(['from' => null, 'limit' => 10], $request->query()->all());
    }
}

class ConcretePaginatedRequest extends PaginatedRequest
{
    protected ?string $hydratableResource = BaseCollection::class;

    public function resolveResourcePath(): string
    {
        return 'test';
    }
}

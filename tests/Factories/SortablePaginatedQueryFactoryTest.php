<?php

declare(strict_types=1);

namespace Tests\Factories;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Factories\SortablePaginatedQueryFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SortablePaginatedQueryFactoryTest extends TestCase
{
    #[Test]
    public function constructor_input_populates_the_query_map_and_nested_sort()
    {
        $query = SortablePaginatedQueryFactory::new([
            'from' => 'tr_123',
            'limit' => 25,
            'filters' => ['sort' => 'desc'],
        ])->create();

        $this->assertSame('tr_123', $query->from);
        $this->assertSame(25, $query->limit);
        $this->assertSame('desc', $query->sort);
    }

    #[Test]
    public function with_query_populates_the_query_map()
    {
        $query = SortablePaginatedQueryFactory::new()
            ->withQuery([
                'from' => 'tr_456',
                'limit' => 50,
                'sort' => 'asc',
            ])
            ->create();

        $this->assertSame('tr_456', $query->from);
        $this->assertSame(50, $query->limit);
        $this->assertSame('asc', $query->sort);
    }

    #[Test]
    public function constructor_input_preserves_falsy_values_over_nested_fallbacks()
    {
        $query = SortablePaginatedQueryFactory::new([
            'from' => '',
            'limit' => 0,
            'sort' => '',
            'filters' => [
                'from' => 'tr_fallback',
                'limit' => 10,
                'sort' => 'desc',
            ],
        ])->create();

        $this->assertSame('', $query->from);
        $this->assertSame(0, $query->limit);
        $this->assertSame('', $query->sort);
    }

    #[Test]
    public function with_query_replaces_constructor_input()
    {
        $query = SortablePaginatedQueryFactory::new([
            'from' => 'tr_constructor',
            'limit' => 10,
            'sort' => 'asc',
        ])->withQuery([
            'from' => 'tr_override',
            'limit' => 20,
            'filters' => ['sort' => 'desc'],
        ])->create();

        $this->assertSame('tr_override', $query->from);
        $this->assertSame(20, $query->limit);
        $this->assertSame('desc', $query->sort);
    }

    #[Test]
    public function constructor_accepts_arrayable_input()
    {
        $input = new class implements Arrayable {
            public function toArray(): array
            {
                return [
                    'from' => 'tr_arrayable',
                    'limit' => 30,
                    'filters' => ['sort' => 'asc'],
                ];
            }
        };

        $query = SortablePaginatedQueryFactory::new($input)->create();

        $this->assertSame('tr_arrayable', $query->from);
        $this->assertSame(30, $query->limit);
        $this->assertSame('asc', $query->sort);
    }
}

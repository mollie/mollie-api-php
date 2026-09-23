<?php

declare(strict_types=1);

namespace Tests\Factories;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Factories\PaginatedQueryFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PaginatedQueryFactoryTest extends TestCase
{
    #[Test]
    public function constructor_input_populates_the_query_map()
    {
        $query = PaginatedQueryFactory::new([
            'from' => 'tr_123',
            'limit' => 25,
        ])->create();

        $this->assertSame('tr_123', $query->from);
        $this->assertSame(25, $query->limit);
    }

    #[Test]
    public function with_query_populates_the_query_map()
    {
        $query = PaginatedQueryFactory::new()
            ->withQuery([
                'from' => 'tr_456',
                'limit' => 50,
            ])
            ->create();

        $this->assertSame('tr_456', $query->from);
        $this->assertSame(50, $query->limit);
    }

    #[Test]
    public function constructor_input_preserves_falsy_values()
    {
        $query = PaginatedQueryFactory::new([
            'from' => '',
            'limit' => 0,
        ])->create();

        $this->assertSame('', $query->from);
        $this->assertSame(0, $query->limit);
    }

    #[Test]
    public function with_query_replaces_constructor_input()
    {
        $query = PaginatedQueryFactory::new([
            'from' => 'tr_constructor',
            'limit' => 10,
        ])->withQuery([
            'from' => 'tr_override',
            'limit' => 20,
        ])->create();

        $this->assertSame('tr_override', $query->from);
        $this->assertSame(20, $query->limit);
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
                ];
            }
        };

        $query = PaginatedQueryFactory::new($input)->create();

        $this->assertSame('tr_arrayable', $query->from);
        $this->assertSame(30, $query->limit);
    }
}

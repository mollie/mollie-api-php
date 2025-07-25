<?php

namespace Tests\Http\Data;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Http\Data\DataCollection;
use PHPUnit\Framework\TestCase;

class DataCollectionTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider provideArraysForConstruction
     */
    public function can_be_constructed_and_converted_to_array($items)
    {
        $collection = new DataCollection($items);

        $this->assertSame($items, $collection->toArray());
    }

    public static function provideArraysForConstruction()
    {
        return [
            'simple array' => [[1, 2, 3]],
            'associative array' => [['a' => 1, 'b' => 2]],
            'empty array' => [[]],
        ];
    }

    /**
     * @test
     *
     * @dataProvider provideArraysForCount
     */
    public function counts_items_correctly($items, $expectedCount)
    {
        $collection = new DataCollection($items);

        $this->assertSame($expectedCount, $collection->count());
    }

    public static function provideArraysForCount()
    {
        return [
            'four items' => [[1, 2, 3, 4], 4],
            'empty array' => [[], 0],
            'single associative' => [['a' => 1], 1],
        ];
    }

    /**
     * @test
     *
     * @dataProvider provideWrapSubjects
     */
    public function wraps_various_subjects_correctly($subject, $expected)
    {
        $collection = DataCollection::wrap($subject);

        $this->assertInstanceOf(DataCollection::class, $collection);
        $this->assertSame($expected, $collection->toArray());
    }

    public static function provideWrapSubjects()
    {
        $mockArrayable = new class implements Arrayable {
            public function toArray(): array
            {
                return [3, 4];
            }
        };

        return [
            'array' => [[1, 2], [1, 2]],
            'arrayable' => [$mockArrayable, [3, 4]],
            'single value' => [5, [5]],
        ];
    }

    /**
     * @test
     *
     * @dataProvider provideValuesCollections
     */
    public function returns_values_collection($input, $expected)
    {
        $collection = new DataCollection($input);

        $values = $collection->values();

        $this->assertSame($expected, $values->toArray());
    }

    public static function provideValuesCollections()
    {
        return [
            'assoc to values' => [['a' => 1, 'b' => 2], [1, 2]],
            'indexed to values' => [[1, 2, 3], [1, 2, 3]],
            'empty to values' => [[], []],
        ];
    }

    /** @test */
    public function can_pipe_through_callback()
    {
        $collection = new DataCollection([1, 2, 3]);

        $result = $collection->pipe(function ($c) {
            return $c->map(fn ($v) => $v * 2);
        });

        $this->assertSame([2, 4, 6], $result->toArray());
    }

    /**
     * @test
     *
     * @dataProvider provideMapCases
     */
    public function maps_items_correctly($input, $callback, $expected)
    {
        $collection = new DataCollection($input);

        $mapped = $collection->map($callback);

        $this->assertSame($expected, $mapped->toArray());
    }

    public static function provideMapCases()
    {
        return [
            'increment' => [[1, 2, 3], fn ($v) => $v + 1, [2, 3, 4]],
            'halve' => [[2, 4, 6], fn ($v) => $v / 2, [1, 2, 3]],
            'empty' => [[], fn ($v) => $v, []],
        ];
    }

    /**
     * @test
     *
     * @dataProvider provideFilterCases
     */
    public function filters_items_correctly($input, $callback, $expected)
    {
        $collection = new DataCollection($input);

        $filtered = $collection->filter($callback);

        $this->assertSame($expected, $filtered->toArray());
    }

    public static function provideFilterCases()
    {
        return [
            'even values' => [[1, 2, 3, 4], fn ($v) => $v % 2 === 0, [1 => 2, 3 => 4]],
            'greater than two' => [[1, 3, 5], fn ($v) => $v > 2, [1 => 3, 2 => 5]],
            'no callback' => [[1, 2, 3], null, [1, 2, 3]],
            'empty input' => [[], fn ($v) => true, []],
        ];
    }

    /**
     * @test
     *
     * @dataProvider provideContainsCases
     */
    public function contains_checks_for_items_correctly($input, $search, $expected)
    {
        $collection = new DataCollection($input);

        $this->assertSame($expected, $collection->contains($search));
    }

    public static function provideContainsCases()
    {
        return [
            // Direct value checks
            'simple value exists' => [[1, 2, 3], 2, true],
            'simple value does not exist' => [[1, 2, 3], 4, false],
            'string value exists' => [['a', 'b', 'c'], 'b', true],
            'string value does not exist' => [['a', 'b', 'c'], 'd', false],
            'empty collection' => [[], 1, false],
            'null value' => [[null], null, true],
            'false value' => [[false], false, true],
            'zero value' => [[0], 0, true],

            // Callback checks
            'callback finds match' => [
                [1, 2, 3],
                fn ($item) => $item > 2,
                true,
            ],
            'callback finds no match' => [
                [1, 2, 3],
                fn ($item) => $item > 3,
                false,
            ],
            'callback with associative array' => [
                ['a' => 1, 'b' => 2],
                fn ($item) => $item === 2,
                true,
            ],
            'empty collection with callback' => [
                [],
                fn ($item) => true,
                false,
            ],
        ];
    }
}

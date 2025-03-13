<?php

namespace Tests\Repositories;

use Mollie\Api\Repositories\QueryStore;
use PHPUnit\Framework\TestCase;

class QueryStoreTest extends TestCase
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public function standardStoreProvider(): array
    {
        return [
            'standard_store' => [
                'data' => [
                    'string' => 'value',
                    'number' => 42,
                    'bool_true' => true,
                    'bool_false' => false,
                    'nested' => [
                        'inner_bool' => true,
                        'inner_string' => 'nested value'
                    ]
                ]
            ],
        ];
    }

    /** @test */
    public function constructor_sets_initial_data()
    {
        $store = new QueryStore(['test' => 'value']);
        $this->assertEquals(['test' => 'value'], $store->all());
    }

    /**
     * @test
     * @dataProvider standardStoreProvider
     */
    public function resolve_transforms_booleans_to_strings(array $data)
    {
        $store = new QueryStore($data);
        $store->resolve();

        // Check individual values instead of the entire array
        $this->assertEquals('value', $store->get('string'));
        $this->assertEquals(42, $store->get('number'));
        $this->assertEquals('true', $store->get('bool_true'));
        $this->assertEquals('false', $store->get('bool_false'));
        $this->assertEquals('true', $store->get('nested.inner_bool'));
        $this->assertEquals('nested value', $store->get('nested.inner_string'));
    }

    /** @test */
    public function resolve_preserves_non_boolean_values()
    {
        $store = new QueryStore([
            'string' => 'value',
            'number' => 42,
            'array' => ['one', 'two']
        ]);

        $store->resolve();

        $this->assertEquals('value', $store->get('string'));
        $this->assertEquals(42, $store->get('number'));
        $this->assertEquals(['one', 'two'], $store->get('array'));
    }

    /**
     * @test
     * @dataProvider standardStoreProvider
     */
    public function inherits_array_store_functionality(array $data)
    {
        $store = new QueryStore($data);

        // Test that QueryStore inherits all ArrayStore functionality
        $store->add('new_key', 'new_value');
        $this->assertEquals('new_value', $store->get('new_key'));

        $store->remove('string');
        $this->assertFalse($store->has('string'));

        $store->merge(['merged' => 'value']);
        $this->assertEquals('value', $store->get('merged'));
    }
}

<?php

namespace Tests\Repositories;

use Mollie\Api\Repositories\ArrayStore;
use PHPUnit\Framework\TestCase;

class ArrayStoreTest extends TestCase
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public function standardStoreProvider(): array
    {
        return [
            'standard_store' => [
                'data' => ['foo' => 'bar', 'nested' => ['key' => 'value']],
            ],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function emptyStoreProvider(): array
    {
        return [
            'empty_store' => [
                'data' => [],
            ],
        ];
    }

    /** @test */
    public function constructor_sets_initial_data()
    {
        $store = new ArrayStore(['test' => 'value']);
        $this->assertEquals(['test' => 'value'], $store->all());
    }

    /**
     * @test
     *
     * @dataProvider standardStoreProvider
     */
    public function set_replaces_all_data(array $data)
    {
        $store = new ArrayStore($data);
        $store->set(['new' => 'data']);
        $this->assertEquals(['new' => 'data'], $store->all());
    }

    /**
     * @test
     *
     * @dataProvider standardStoreProvider
     */
    public function get_returns_value_by_key(array $data)
    {
        $store = new ArrayStore($data);
        $this->assertEquals('bar', $store->get('foo'));
    }

    /**
     * @test
     *
     * @dataProvider standardStoreProvider
     */
    public function get_returns_nested_value_by_dot_notation(array $data)
    {
        $store = new ArrayStore($data);
        $this->assertEquals('value', $store->get('nested.key'));
    }

    /**
     * @test
     *
     * @dataProvider standardStoreProvider
     */
    public function get_returns_default_when_key_not_found(array $data)
    {
        $store = new ArrayStore($data);
        $this->assertEquals('default', $store->get('missing', 'default'));
    }

    /**
     * @test
     *
     * @dataProvider standardStoreProvider
     */
    public function add_adds_new_key_value_pair(array $data)
    {
        $store = new ArrayStore($data);
        $store->add('new', 'value');
        $this->assertEquals('value', $store->get('new'));
    }

    /**
     * @test
     *
     * @dataProvider standardStoreProvider
     */
    public function has_returns_true_when_key_exists(array $data)
    {
        $store = new ArrayStore($data);
        $this->assertTrue($store->has('foo'));
    }

    /**
     * @test
     *
     * @dataProvider standardStoreProvider
     */
    public function has_returns_true_when_nested_key_exists(array $data)
    {
        $store = new ArrayStore($data);
        $this->assertTrue($store->has('nested.key'));
    }

    /**
     * @test
     *
     * @dataProvider standardStoreProvider
     */
    public function has_returns_false_when_key_does_not_exist(array $data)
    {
        $store = new ArrayStore($data);
        $this->assertFalse($store->has('missing'));
    }

    /**
     * @test
     *
     * @dataProvider standardStoreProvider
     */
    public function merge_merges_arrays_into_store(array $data)
    {
        $store = new ArrayStore($data);
        $store->merge(['new' => 'value'], ['another' => 'value2']);
        $this->assertEquals('value', $store->get('new'));
        $this->assertEquals('value2', $store->get('another'));
        $this->assertEquals('bar', $store->get('foo')); // Original data still exists
    }

    /**
     * @test
     *
     * @dataProvider standardStoreProvider
     */
    public function remove_removes_key_from_store(array $data)
    {
        $store = new ArrayStore($data);
        $store->remove('foo');
        $this->assertFalse($store->has('foo'));
    }

    /**
     * @test
     *
     * @dataProvider standardStoreProvider
     */
    public function remove_removes_nested_key_from_store(array $data)
    {
        $store = new ArrayStore($data);
        $store->remove('nested.key');
        $this->assertFalse($store->has('nested.key'));
    }

    /**
     * @test
     *
     * @dataProvider standardStoreProvider
     */
    public function all_returns_all_data(array $data)
    {
        $store = new ArrayStore($data);
        $this->assertEquals($data, $store->all());
    }

    /**
     * @test
     *
     * @dataProvider emptyStoreProvider
     */
    public function is_empty_returns_true_when_store_is_empty(array $data)
    {
        $store = new ArrayStore($data);
        $this->assertTrue($store->isEmpty());
    }

    /**
     * @test
     *
     * @dataProvider standardStoreProvider
     */
    public function is_empty_returns_false_when_store_is_not_empty(array $data)
    {
        $store = new ArrayStore($data);
        $this->assertFalse($store->isEmpty());
    }

    /**
     * @test
     *
     * @dataProvider standardStoreProvider
     */
    public function is_not_empty_returns_true_when_store_is_not_empty(array $data)
    {
        $store = new ArrayStore($data);
        $this->assertTrue($store->isNotEmpty());
    }

    /**
     * @test
     *
     * @dataProvider emptyStoreProvider
     */
    public function is_not_empty_returns_false_when_store_is_empty(array $data)
    {
        $store = new ArrayStore($data);
        $this->assertFalse($store->isNotEmpty());
    }
}

<?php

namespace Tests\Repositories;

use Mollie\Api\Repositories\JsonPayloadRepository;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

class JsonPayloadRepositoryTest extends TestCase
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public function standardRepositoryProvider(): array
    {
        return [
            'standard_repository' => [
                'data' => ['foo' => 'bar', 'nested' => ['key' => 'value']],
            ],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function emptyRepositoryProvider(): array
    {
        return [
            'empty_repository' => [
                'data' => [],
            ],
        ];
    }

    /** @test */
    public function constructor_sets_initial_data()
    {
        $repository = new JsonPayloadRepository(['test' => 'value']);
        $this->assertEquals(['test' => 'value'], $repository->all());
    }

    /** @test */
    public function constructor_with_empty_array_creates_empty_repository()
    {
        $repository = new JsonPayloadRepository;
        $this->assertEquals([], $repository->all());
    }

    /**
     * @test
     *
     * @dataProvider standardRepositoryProvider
     */
    public function has_returns_true_when_key_exists(array $data)
    {
        $repository = new JsonPayloadRepository($data);
        $this->assertTrue($repository->has('foo'));
    }

    /**
     * @test
     *
     * @dataProvider standardRepositoryProvider
     */
    public function has_returns_false_when_key_does_not_exist(array $data)
    {
        $repository = new JsonPayloadRepository($data);
        $this->assertFalse($repository->has('missing'));
    }

    /**
     * @test
     *
     * @dataProvider standardRepositoryProvider
     */
    public function set_replaces_all_data(array $data)
    {
        $repository = new JsonPayloadRepository($data);
        $repository->set(['new' => 'data']);
        $this->assertEquals(['new' => 'data'], $repository->all());
    }

    /**
     * @test
     *
     * @dataProvider standardRepositoryProvider
     */
    public function all_returns_all_data(array $data)
    {
        $repository = new JsonPayloadRepository($data);
        $this->assertEquals($data, $repository->all());
    }

    /**
     * @test
     *
     * @dataProvider standardRepositoryProvider
     */
    public function add_adds_new_key_value_pair(array $data)
    {
        $repository = new JsonPayloadRepository($data);
        $repository->add('new', 'value');
        $this->assertEquals('value', $repository->get('new'));
    }

    /**
     * @test
     *
     * @dataProvider standardRepositoryProvider
     */
    public function get_returns_value_by_key(array $data)
    {
        $repository = new JsonPayloadRepository($data);
        $this->assertEquals('bar', $repository->get('foo'));
    }

    /**
     * @test
     *
     * @dataProvider standardRepositoryProvider
     */
    public function get_returns_default_when_key_not_found(array $data)
    {
        $repository = new JsonPayloadRepository($data);
        $this->assertEquals('default', $repository->get('missing', 'default'));
    }

    /**
     * @test
     *
     * @dataProvider standardRepositoryProvider
     */
    public function merge_merges_arrays_into_repository(array $data)
    {
        $repository = new JsonPayloadRepository($data);
        $repository->merge(['new' => 'value'], ['another' => 'value2']);
        $this->assertEquals('value', $repository->get('new'));
        $this->assertEquals('value2', $repository->get('another'));
        $this->assertEquals('bar', $repository->get('foo')); // Original data still exists
    }

    /**
     * @test
     *
     * @dataProvider standardRepositoryProvider
     */
    public function remove_removes_key_from_repository(array $data)
    {
        $repository = new JsonPayloadRepository($data);
        $repository->remove('foo');
        $this->assertFalse($repository->has('foo'));
    }

    /**
     * @test
     *
     * @dataProvider emptyRepositoryProvider
     */
    public function is_empty_returns_true_when_repository_is_empty(array $data)
    {
        $repository = new JsonPayloadRepository($data);
        $this->assertTrue($repository->isEmpty());
    }

    /**
     * @test
     *
     * @dataProvider standardRepositoryProvider
     */
    public function is_empty_returns_false_when_repository_is_not_empty(array $data)
    {
        $repository = new JsonPayloadRepository($data);
        $this->assertFalse($repository->isEmpty());
    }

    /**
     * @test
     *
     * @dataProvider standardRepositoryProvider
     */
    public function is_not_empty_returns_true_when_repository_is_not_empty(array $data)
    {
        $repository = new JsonPayloadRepository($data);
        $this->assertTrue($repository->isNotEmpty());
    }

    /**
     * @test
     *
     * @dataProvider emptyRepositoryProvider
     */
    public function is_not_empty_returns_false_when_repository_is_empty(array $data)
    {
        $repository = new JsonPayloadRepository($data);
        $this->assertFalse($repository->isNotEmpty());
    }

    /**
     * @test
     *
     * @dataProvider standardRepositoryProvider
     */
    public function to_string_returns_json_encoded_data(array $data)
    {
        $repository = new JsonPayloadRepository($data);
        $this->assertEquals('{"foo":"bar","nested":{"key":"value"}}', (string) $repository);
    }

    /**
     * @test
     *
     * @dataProvider standardRepositoryProvider
     */
    public function to_stream_returns_stream_with_json_data(array $data)
    {
        $repository = new JsonPayloadRepository($data);

        // Create mock stream
        $stream = $this->createMock(StreamInterface::class);

        // Create a concrete implementation of StreamFactoryInterface
        $streamFactory = new class($stream) implements StreamFactoryInterface {
            private $stream;

            public function __construct($stream)
            {
                $this->stream = $stream;
            }

            public function createStream(string $content = ''): StreamInterface
            {
                return $this->stream;
            }

            public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
            {
                return $this->stream;
            }

            public function createStreamFromResource($resource): StreamInterface
            {
                return $this->stream;
            }
        };

        // Call the method under test
        $result = $repository->toStream($streamFactory);

        // Assert the result is the same as our mocked stream
        $this->assertSame($stream, $result);
    }
}

<?php

declare(strict_types=1);

namespace Tests\Repositories;

use Mollie\Api\Repositories\JsonPayloadRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

class JsonPayloadRepositoryTest extends TestCase
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function standardRepositoryProvider(): array
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
    public static function emptyRepositoryProvider(): array
    {
        return [
            'empty_repository' => [
                'data' => [],
            ],
        ];
    }

    #[Test]
    public function constructor_sets_initial_data()
    {
        $repository = new JsonPayloadRepository(['test' => 'value']);
        $this->assertEquals(['test' => 'value'], $repository->all());
    }

    #[Test]
    public function constructor_with_empty_array_creates_empty_repository()
    {
        $repository = new JsonPayloadRepository;
        $this->assertEquals([], $repository->all());
    }

    #[DataProvider('standardRepositoryProvider')]
    #[Test]
    public function has_returns_true_when_key_exists(array $data)
    {
        $repository = new JsonPayloadRepository($data);
        $this->assertTrue($repository->has('foo'));
    }

    #[DataProvider('standardRepositoryProvider')]
    #[Test]
    public function has_returns_false_when_key_does_not_exist(array $data)
    {
        $repository = new JsonPayloadRepository($data);
        $this->assertFalse($repository->has('missing'));
    }

    #[DataProvider('standardRepositoryProvider')]
    #[Test]
    public function set_replaces_all_data(array $data)
    {
        $repository = new JsonPayloadRepository($data);
        $repository->set(['new' => 'data']);
        $this->assertEquals(['new' => 'data'], $repository->all());
    }

    #[DataProvider('standardRepositoryProvider')]
    #[Test]
    public function all_returns_all_data(array $data)
    {
        $repository = new JsonPayloadRepository($data);
        $this->assertEquals($data, $repository->all());
    }

    #[DataProvider('standardRepositoryProvider')]
    #[Test]
    public function add_adds_new_key_value_pair(array $data)
    {
        $repository = new JsonPayloadRepository($data);
        $repository->add('new', 'value');
        $this->assertEquals('value', $repository->get('new'));
    }

    #[DataProvider('standardRepositoryProvider')]
    #[Test]
    public function get_returns_value_by_key(array $data)
    {
        $repository = new JsonPayloadRepository($data);
        $this->assertEquals('bar', $repository->get('foo'));
    }

    #[DataProvider('standardRepositoryProvider')]
    #[Test]
    public function get_returns_default_when_key_not_found(array $data)
    {
        $repository = new JsonPayloadRepository($data);
        $this->assertEquals('default', $repository->get('missing', 'default'));
    }

    #[DataProvider('standardRepositoryProvider')]
    #[Test]
    public function merge_merges_arrays_into_repository(array $data)
    {
        $repository = new JsonPayloadRepository($data);
        $repository->merge(['new' => 'value'], ['another' => 'value2']);
        $this->assertEquals('value', $repository->get('new'));
        $this->assertEquals('value2', $repository->get('another'));
        $this->assertEquals('bar', $repository->get('foo')); // Original data still exists
    }

    #[DataProvider('standardRepositoryProvider')]
    #[Test]
    public function remove_removes_key_from_repository(array $data)
    {
        $repository = new JsonPayloadRepository($data);
        $repository->remove('foo');
        $this->assertFalse($repository->has('foo'));
    }

    #[DataProvider('emptyRepositoryProvider')]
    #[Test]
    public function is_empty_returns_true_when_repository_is_empty(array $data)
    {
        $repository = new JsonPayloadRepository($data);
        $this->assertTrue($repository->isEmpty());
    }

    #[DataProvider('standardRepositoryProvider')]
    #[Test]
    public function is_empty_returns_false_when_repository_is_not_empty(array $data)
    {
        $repository = new JsonPayloadRepository($data);
        $this->assertFalse($repository->isEmpty());
    }

    #[DataProvider('standardRepositoryProvider')]
    #[Test]
    public function is_not_empty_returns_true_when_repository_is_not_empty(array $data)
    {
        $repository = new JsonPayloadRepository($data);
        $this->assertTrue($repository->isNotEmpty());
    }

    #[DataProvider('emptyRepositoryProvider')]
    #[Test]
    public function is_not_empty_returns_false_when_repository_is_empty(array $data)
    {
        $repository = new JsonPayloadRepository($data);
        $this->assertFalse($repository->isNotEmpty());
    }

    #[DataProvider('standardRepositoryProvider')]
    #[Test]
    public function to_string_returns_json_encoded_data(array $data)
    {
        $repository = new JsonPayloadRepository($data);
        $this->assertEquals('{"foo":"bar","nested":{"key":"value"}}', (string) $repository);
    }

    #[DataProvider('standardRepositoryProvider')]
    #[Test]
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

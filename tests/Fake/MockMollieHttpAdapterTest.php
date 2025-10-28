<?php

namespace Tests\Fake;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockMollieHttpAdapter;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Fake\SequenceMockResponse;
use Mollie\Api\Http\PendingRequest;
use Mollie\Api\Http\Response;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Tests\Fixtures\Requests\DynamicGetRequest;

class MockMollieHttpAdapterTest extends TestCase
{
    /** @test */
    public function it_returns_mocked_response_for_expected_request()
    {
        $adapter = new MockMollieHttpAdapter([
            DynamicGetRequest::class => MockResponse::ok(['test' => 'data']),
        ]);

        $pendingRequest = new PendingRequest(new MockMollieClient, new DynamicGetRequest(''));

        $response = $adapter->sendRequest($pendingRequest);

        $this->assertEquals(200, $response->status());
        $this->assertEquals('{"test":"data"}', $response->body());
    }

    /** @test */
    public function it_can_retain_requests()
    {
        $adapter = new MockMollieHttpAdapter([
            DynamicGetRequest::class => MockResponse::ok(['test' => 'data']),
        ], true);

        $pendingRequest = new PendingRequest(new MockMollieClient, new DynamicGetRequest(''));

        $response = $adapter->sendRequest($pendingRequest);
        $response2 = $adapter->sendRequest($pendingRequest);

        $this->assertEquals(200, $response->status());
        $this->assertEquals('{"test":"data"}', $response->body());
        $this->assertEquals('{"test":"data"}', $response2->body());
    }

    /** @test */
    public function can_handle_callback_for_expected_response()
    {
        $adapter = new MockMollieHttpAdapter([
            DynamicGetRequest::class => fn (PendingRequest $pendingRequest) => MockResponse::ok([
                'test' => $pendingRequest->query()->get('test'),
            ]),
        ]);

        $pendingRequest = new PendingRequest(new MockMollieClient, new DynamicGetRequest('', ['test' => 'data']));

        $response = $adapter->sendRequest($pendingRequest);

        $this->assertEquals(200, $response->status());
        $this->assertEquals('{"test":"data"}', $response->body());
    }

    /** @test */
    public function it_throws_exception_for_unexpected_request()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The request class '.DynamicGetRequest::class.' is not expected.');

        $adapter = new MockMollieHttpAdapter([]);
        $pendingRequest = new PendingRequest(new MockMollieClient, new DynamicGetRequest(''));

        $adapter->sendRequest($pendingRequest);
    }

    /** @test */
    public function it_handles_sequence_mock_responses()
    {
        $adapter = new MockMollieHttpAdapter([
            DynamicGetRequest::class => new SequenceMockResponse(
                MockResponse::ok(['first' => 'response']),
                MockResponse::ok(['second' => 'response']),
            ),
        ]);

        $pendingRequest = new PendingRequest(new MockMollieClient, new DynamicGetRequest(''));

        // First request
        /** @var Response $response */
        $response = $adapter->sendRequest($pendingRequest);
        $this->assertEquals('{"first":"response"}', $response->body());

        // Second request
        $response2 = $adapter->sendRequest($pendingRequest);
        $this->assertEquals('{"second":"response"}', $response2->body());
    }

    /** @test */
    public function it_records_requests_and_can_assert_on_them()
    {
        $adapter = new MockMollieHttpAdapter([
            DynamicGetRequest::class => MockResponse::ok(),
        ]);

        $pendingRequest = new PendingRequest(new MockMollieClient, new DynamicGetRequest(''));

        $adapter->sendRequest($pendingRequest);

        // Test assertSent with class name
        $adapter->assertSent(DynamicGetRequest::class);

        // Test assertSent with callback
        $adapter->assertSent(function (PendingRequest $request) use ($pendingRequest) {
            return $request->getRequest() === $pendingRequest->getRequest();
        });

        // Test assertSentCount
        $adapter->assertSentCount(1);
    }

    /** @test */
    public function it_returns_recorded_requests()
    {
        $adapter = new MockMollieHttpAdapter([
            DynamicGetRequest::class => MockResponse::ok(),
        ]);

        $pendingRequest = new PendingRequest(new MockMollieClient, new DynamicGetRequest(''));

        $adapter->sendRequest($pendingRequest);

        // Test recorded without callback
        $recorded = $adapter->recorded();
        $this->assertCount(1, $recorded);
        $this->assertSame($pendingRequest->getRequest(), $recorded[0][0]->getRequest());

        // Test recorded with callback
        $adapter->assertSent(function (PendingRequest $request) use ($pendingRequest) {
            return $request->getRequest() === $pendingRequest->getRequest();
        });
    }
}

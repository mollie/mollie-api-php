<?php

namespace Tests\Fake;

use Mollie\Api\Fake\MockResponse;
use PHPUnit\Framework\TestCase;

class MockResponseTest extends TestCase
{
    /** @test */
    public function body_returns_json_string_directly()
    {
        $jsonString = '{"key":"value"}';
        $response = new MockResponse($jsonString);

        $this->assertEquals($jsonString, $response->body());
    }

    /** @test */
    public function body_converts_array_to_json_string()
    {
        $array = ['key' => 'value'];
        $response = new MockResponse($array);

        $this->assertEquals(json_encode($array), $response->body());
    }

    /** @test */
    public function body_loads_json_file_when_not_json_string()
    {
        $response = new MockResponse('payment');

        $body = $response->body();

        $this->assertJson($body);
        $this->assertStringContainsString('payment', $body);
    }

    /** @test */
    public function body_replaces_resource_id_placeholder()
    {
        $response = new MockResponse('payment', 200, 'tr_12345');

        $body = $response->body();

        $this->assertStringContainsString('tr_12345', $body);
        $this->assertStringNotContainsString('{{ RESOURCE_ID }}', $body);
    }

    /** @test */
    public function assert_response_body_equals_compares_response_contents()
    {
        $response = MockResponse::ok('{"test": true}');
        $psrResponse = $response->createPsrResponse();

        $response->assertResponseBodyEquals($psrResponse);
    }
}

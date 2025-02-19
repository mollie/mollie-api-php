<?php

namespace Tests\Fake;

use Mollie\Api\Fake\ListResponseBuilder;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Fake\ResourceResponseBuilder;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
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
    public function list_returns_list_builder()
    {
        $response = MockResponse::list(PaymentCollection::class);

        $this->assertInstanceOf(ListResponseBuilder::class, $response);
    }

    /** @test */
    public function resource_returns_resource_builder()
    {
        $response = MockResponse::resource(Payment::class);

        $this->assertInstanceOf(ResourceResponseBuilder::class, $response);
    }

    /** @test */
    public function not_found_returns_404_status_code()
    {
        $response = MockResponse::notFound();

        $this->assertEquals(404, $response->createPsrResponse()->getStatusCode());
    }

    /** @test */
    public function unprocessable_entity_returns_422_status_code()
    {
        $response = MockResponse::unprocessableEntity();

        $this->assertEquals(422, $response->createPsrResponse()->getStatusCode());
    }
}

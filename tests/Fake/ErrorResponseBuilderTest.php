<?php

namespace Mollie\Api\Tests\Fake;

use Mollie\Api\Fake\ErrorResponseBuilder;
use Mollie\Api\Fake\MockResponse;
use PHPUnit\Framework\TestCase;

class ErrorResponseBuilderTest extends TestCase
{
    /** @test */
    public function can_create_a_response_without_field()
    {
        $response = (new ErrorResponseBuilder(404, 'Not Found', 'No payment exists with token tr_xxxxxxxxxxx.'))->create();

        $this->assertInstanceOf(MockResponse::class, $response);
        $this->assertEquals(404, $response->createPsrResponse()->getStatusCode());

        $data = $response->json();
        $this->assertEquals(404, $data['status']);
        $this->assertEquals('Not Found', $data['title']);
        $this->assertEquals('No payment exists with token tr_xxxxxxxxxxx.', $data['detail']);
        $this->assertArrayNotHasKey('field', $data);
    }

    /** @test */
    public function can_create_a_response_with_field()
    {
        $response = (new ErrorResponseBuilder(404, 'Not Found', 'No payment exists with token tr_xxxxxxxxxxx.', 'field'))->create();

        $this->assertInstanceOf(MockResponse::class, $response);
        $this->assertEquals(404, $response->createPsrResponse()->getStatusCode());

        $data = $response->json();
        $this->assertEquals(404, $data['status']);
        $this->assertEquals('Not Found', $data['title']);
        $this->assertEquals('No payment exists with token tr_xxxxxxxxxxx.', $data['detail']);
        $this->assertEquals('field', $data['field']);
    }

    /** @test */
    public function can_handle_special_characters_in_detail()
    {
        $detail = 'Non-existent parameter "recurringType" for this API call. Did you mean: "sequenceType"?';
        $response = (new ErrorResponseBuilder(422, 'Unprocessable Entity', $detail))->create();

        $this->assertInstanceOf(MockResponse::class, $response);
        $this->assertEquals(422, $response->createPsrResponse()->getStatusCode());

        $data = $response->json();
        $this->assertEquals(422, $data['status']);
        $this->assertEquals('Unprocessable Entity', $data['title']);
        $this->assertEquals($detail, $data['detail']);
        $this->assertArrayNotHasKey('field', $data);
    }
}

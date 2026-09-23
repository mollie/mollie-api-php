<?php

declare(strict_types=1);

namespace Mollie\Api\Tests\Fake;

use Mollie\Api\Fake\ErrorResponseBuilder;
use Mollie\Api\Fake\MockResponse;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ErrorResponseBuilderTest extends TestCase
{
    #[Test]
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

    #[Test]
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

    #[Test]
    public function caller_values_round_trip_through_structural_json()
    {
        $characters = 'quote " slash \\ CR'."\r".'LF'."\n".'TAB'."\t"
            .' control '.chr(1).' Unicode ü {{ RESOURCE_ID }}';
        $title = 'Title '.$characters;
        $detail = 'Detail '.$characters;
        $field = 'Field '.$characters;
        $response = (new ErrorResponseBuilder(422, $title, $detail, $field))->create();

        $this->assertInstanceOf(MockResponse::class, $response);
        $this->assertEquals(422, $response->createPsrResponse()->getStatusCode());

        $data = $response->json();
        $this->assertEquals(422, $data['status']);
        $this->assertSame($title, $data['title']);
        $this->assertSame($detail, $data['detail']);
        $this->assertSame($field, $data['field']);
    }
}

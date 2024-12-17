<?php

namespace Tests\Http\Middleware;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Http\Middleware\GuardResponse;
use Mollie\Api\Http\Response;
use Mollie\Api\Http\ResponseStatusCode;
use PHPUnit\Framework\TestCase;

class GuardResponseTest extends TestCase
{
    /**
     * @test
     */
    public function it_throws_exception_if_no_response_body_and_not_http_no_content(): void
    {
        $responseMock = $this->createMock(Response::class);

        // Mock the status method to return a status other than HTTP_NO_CONTENT
        $responseMock->expects($this->once())
            ->method('status')
            ->willReturn(ResponseStatusCode::HTTP_OK);

        // Mock the body method to return an empty body
        $responseMock->expects($this->once())
            ->method('isEmpty')
            ->willReturn(true);

        $guardResponse = new GuardResponse;

        // Expect the ApiException to be thrown due to no response body
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('No response body found.');

        $guardResponse($responseMock);
    }

    /**
     * @test
     */
    public function it_does_not_throw_exception_if_http_no_content(): void
    {
        $responseMock = $this->createMock(Response::class);

        // Mock the status method to return HTTP_NO_CONTENT
        $responseMock->expects($this->once())
            ->method('status')
            ->willReturn(ResponseStatusCode::HTTP_NO_CONTENT);

        // Mock the body method to return an empty body
        $responseMock->expects($this->once())
            ->method('isEmpty')
            ->willReturn(true);

        $guardResponse = new GuardResponse;

        // No exception should be thrown
        $guardResponse($responseMock);
    }

    /**
     * @test
     */
    public function it_throws_exception_if_response_contains_error_message(): void
    {
        $responseMock = $this->createMock(Response::class);

        // Mock the json method to return an error object
        $responseMock->expects($this->once())
            ->method('json')
            ->willReturn((object) ['error' => (object) ['message' => 'Some error occurred']]);

        $guardResponse = new GuardResponse;

        // Expect the ApiException to be thrown due to error in the response
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Some error occurred');

        $guardResponse($responseMock);
    }

    /**
     * @test
     */
    public function it_passes_if_valid_json_and_no_error_message(): void
    {
        $responseMock = $this->createMock(Response::class);

        // Mock the json method to return valid data
        $responseMock->expects($this->once())
            ->method('json')
            ->willReturn((object) ['data' => 'valid']);

        $guardResponse = new GuardResponse;

        // No exception should be thrown
        $guardResponse($responseMock);

        // If the test reaches here without exceptions, it passes
        $this->assertTrue(true);
    }
}

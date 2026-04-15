<?php

declare(strict_types=1);

namespace Tests\Exceptions;

use GuzzleHttp\Psr7\Request;
use Mollie\Api\Exceptions\ValidationException;
use Mollie\Api\Http\Response;
use PHPUnit\Framework\TestCase;

class ValidationExceptionTest extends TestCase
{
    /** @test */
    public function exposes_field_and_single_error_from_standard_body(): void
    {
        $response = $this->mockResponseWithBody((object) [
            'status' => 422,
            'title' => 'Unprocessable Entity',
            'detail' => 'The amount is required.',
            'field' => 'amount',
        ]);

        $exception = ValidationException::fromResponse($response);

        self::assertSame('amount', $exception->field);
        self::assertSame('amount', $exception->getField());
        self::assertTrue($exception->hasError('amount'));
        self::assertSame('The amount is required.', $exception->getError('amount'));
        self::assertFalse($exception->hasError('method'));
        self::assertNull($exception->getError('method'));
        self::assertSame(['amount' => 'The amount is required.'], $exception->errors);
    }

    /** @test */
    public function exposes_multiple_errors_from_details_object(): void
    {
        $response = $this->mockResponseWithBody((object) [
            'status' => 422,
            'title' => 'Unprocessable Entity',
            'detail' => 'Validation failed.',
            'field' => 'amount',
            'details' => (object) [
                'amount' => 'Amount is required.',
                'method' => 'Method is invalid.',
            ],
        ]);

        $exception = ValidationException::fromResponse($response);

        self::assertTrue($exception->hasError('amount'));
        self::assertTrue($exception->hasError('method'));
        self::assertSame('Amount is required.', $exception->getError('amount'));
        self::assertSame('Method is invalid.', $exception->getError('method'));
    }

    /** @test */
    public function exposes_errors_from_list_of_field_message_objects(): void
    {
        $response = $this->mockResponseWithBody((object) [
            'status' => 422,
            'title' => 'Unprocessable Entity',
            'detail' => 'Validation failed.',
            'field' => '',
            'errors' => [
                (object) ['field' => 'amount.value', 'message' => 'Must be positive.'],
                (object) ['field' => 'description', 'message' => 'Too long.'],
            ],
        ]);

        $exception = ValidationException::fromResponse($response);

        self::assertSame('Must be positive.', $exception->getError('amount.value'));
        self::assertSame('Too long.', $exception->getError('description'));
        self::assertFalse($exception->hasError('unknown'));
    }

    /** @test */
    public function handles_missing_field_gracefully(): void
    {
        $response = $this->mockResponseWithBody((object) [
            'status' => 422,
            'title' => 'Unprocessable Entity',
            'detail' => 'Something went wrong.',
        ]);

        $exception = ValidationException::fromResponse($response);

        self::assertSame('', $exception->field);
        self::assertSame([], $exception->errors);
    }

    private function mockResponseWithBody(object $body): Response
    {
        $response = $this->createMock(Response::class);
        $response->method('json')->willReturn($body);
        $response->method('status')->willReturn(422);
        $response->method('getPsrRequest')->willReturn(new Request('POST', ''));

        return $response;
    }
}

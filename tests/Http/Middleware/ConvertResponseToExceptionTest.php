<?php

namespace Tests\Http\Middleware;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Exceptions\ForbiddenException;
use Mollie\Api\Exceptions\MethodNotAllowedException;
use Mollie\Api\Exceptions\NotFoundException;
use Mollie\Api\Exceptions\RequestTimeoutException;
use Mollie\Api\Exceptions\ServiceUnavailableException;
use Mollie\Api\Exceptions\TooManyRequestsException;
use Mollie\Api\Exceptions\UnauthorizedException;
use Mollie\Api\Exceptions\ValidationException;
use Mollie\Api\Http\Middleware\ConvertResponseToException;
use Mollie\Api\Http\Response;
use Mollie\Api\Http\ResponseStatusCode;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ConvertResponseToExceptionTest extends TestCase
{
    /**
     * @dataProvider exceptionProvider
     */
    public function test_it_throws_the_correct_exception($statusCode, $expectedException, $field)
    {
        /** @var Response&MockObject $response */
        $response = $this->createMock(Response::class);
        $response->method('successful')->willReturn(false);
        $response->method('status')->willReturn($statusCode);
        $response->method('json')->willReturn((object)[
            'title' => 'Test',
            'detail' => 'Test detail',
            'field' => $field,
        ]);
        $response->method('isEmpty')->willReturn(false);
        $response->method('body')->willReturn('body');
        $response->method('getPsrRequest')->willReturn($this->createMock(\Psr\Http\Message\RequestInterface::class));
        $response->method('getPendingRequest')->willReturn($this->createMock(\Mollie\Api\Http\PendingRequest::class));

        $middleware = new ConvertResponseToException();

        $this->expectException($expectedException);
        $middleware->__invoke($response);
    }

    public static function exceptionProvider()
    {
        $statuses = [
            [ResponseStatusCode::HTTP_UNAUTHORIZED, UnauthorizedException::class],
            [ResponseStatusCode::HTTP_FORBIDDEN, ForbiddenException::class],
            [ResponseStatusCode::HTTP_NOT_FOUND, NotFoundException::class],
            [ResponseStatusCode::HTTP_METHOD_NOT_ALLOWED, MethodNotAllowedException::class],
            [ResponseStatusCode::HTTP_REQUEST_TIMEOUT, RequestTimeoutException::class],
            [ResponseStatusCode::HTTP_UNPROCESSABLE_ENTITY, ValidationException::class],
            [ResponseStatusCode::HTTP_TOO_MANY_REQUESTS, TooManyRequestsException::class],
            [ResponseStatusCode::HTTP_SERVICE_UNAVAILABLE, ServiceUnavailableException::class],
            [999, ApiException::class], // default case
        ];
        $fields = [null, 'foo'];
        $cases = [];
        foreach ($statuses as [$status, $exception]) {
            foreach ($fields as $field) {
                $cases[] = [$status, $exception, $field];
            }
        }
        return $cases;
    }
}

<?php

namespace Tests\Http\Middleware;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Contracts\ViableResponse;
use Mollie\Api\Http\Middleware\Handlers;
use Mollie\Api\Http\Middleware\MiddlewarePriority;
use PHPUnit\Framework\TestCase;

class HandlersTest extends TestCase
{
    /** @test */
    public function add(): void
    {
        $handlers = new Handlers;
        $handlers->add(fn () => null);

        $this->assertCount(1, $handlers->getHandlers());
    }

    /** @test */
    public function handlers_are_executed_in_the_correct_order(): void
    {
        $output = [];

        $handlers = new Handlers;
        $handlers->add(function ($value) use (&$output) {
            $output[] = 1;

            return new TestViableResponse($output);
        }, 'a', MiddlewarePriority::LOW);

        $handlers->add(function () use (&$output) {
            $output[] = 2;
        }, 'b', MiddlewarePriority::MEDIUM);

        $handlers->add(function () use (&$output) {
            $output[] = 3;
        }, 'c', MiddlewarePriority::HIGH);

        $this->assertCount(3, $handlers->getHandlers());

        /** @var TestViableResponse $response */
        $response = $handlers->execute($output);

        $this->assertInstanceOf(TestViableResponse::class, $response);
        $this->assertEquals([3, 2, 1], $response->toArray());
    }
}

class TestViableResponse implements Arrayable, ViableResponse
{
    public array $output = [];

    public function __construct(array $output)
    {
        $this->output = $output;
    }

    public function toArray(): array
    {
        return $this->output;
    }
}

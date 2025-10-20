<?php

namespace Mollie\Api\Fake;

use Closure;

class SequenceMockResponse
{
    /**
     * @var array<MockResponse>
     */
    private array $responses;

    private int $index = 0;

    public function __construct(...$responses)
    {
        $this->responses = $responses;
    }

    /**
     * @return Closure|MockResponse
     */
    public function pop()
    {
        if (! isset($this->responses[$this->index])) {
            throw new \RuntimeException('No more responses available.');
        }

        $response = $this->responses[$this->index];

        unset($this->responses[$this->index]);

        $this->index++;

        return $response;
    }

    public function isEmpty(): bool
    {
        return count($this->responses) === 0;
    }
}

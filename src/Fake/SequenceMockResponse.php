<?php

namespace Mollie\Api\Fake;

use Closure;

class SequenceMockResponse
{
    /**
     * @var array<Closure|MockResponse>
     */
    private array $responses;

    public function __construct(...$responses)
    {
        $this->responses = $responses;
    }

    /**
     * @return Closure|MockResponse
     */
    public function shift()
    {
        if (empty($this->responses)) {
            throw new \RuntimeException('No more responses available.');
        }

        $response = array_shift($this->responses);

        return $response;
    }

    /**
     * @deprecated use shift instead
     * @return Closure|MockResponse
     */
    public function pop()
    {
        return $this->shift();
    }

    public function isEmpty(): bool
    {
        return empty($this->responses);
    }
}

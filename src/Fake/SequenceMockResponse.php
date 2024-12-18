<?php

namespace Mollie\Api\Fake;

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

    public function pop(): MockResponse
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

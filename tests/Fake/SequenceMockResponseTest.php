<?php

namespace Tests\Fake;

use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Fake\SequenceMockResponse;
use PHPUnit\Framework\TestCase;

class SequenceMockResponseTest extends TestCase
{
    /** @test */
    public function it_regognizes_when_no_responses_are_left()
    {
        $sequence = new SequenceMockResponse();

        $this->assertTrue($sequence->isEmpty());
    }

    /** @test */
    public function it_forgets_about_the_last_response_returned()
    {
        $sequence = new SequenceMockResponse(
            MockResponse::created('payment'),
            MockResponse::ok(['foo' => 'bar'])
        );

        $sequence->shift();
        $this->assertFalse($sequence->isEmpty());

        $sequence->shift();
        $this->assertTrue($sequence->isEmpty());
    }
}

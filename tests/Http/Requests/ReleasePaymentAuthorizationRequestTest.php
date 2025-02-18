<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\ReleasePaymentAuthorizationRequest;
use Mollie\Api\Http\Response;
use PHPUnit\Framework\TestCase;

class ReleasePaymentAuthorizationRequestTest extends TestCase
{
    /** @test */
    public function it_can_release_payment_authorization()
    {
        $client = new MockMollieClient([
            ReleasePaymentAuthorizationRequest::class => MockResponse::created(''),
        ]);

        $request = new ReleasePaymentAuthorizationRequest('tr_WDqYK6vllg');

        /** @var Response $response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
    }
}

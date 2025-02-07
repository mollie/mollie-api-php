<?php

namespace Mollie\Api\Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\DynamicPostRequest;
use Mollie\Api\Http\Response;
use PHPUnit\Framework\TestCase;

class ReleasePaymentAuthorizationTest extends TestCase
{
    /** @test */
    public function it_can_release_payment_authorization()
    {
        $client = new MockMollieClient([
            DynamicPostRequest::class => MockResponse::created(''),
        ]);

        $request = new DynamicPostRequest('payments/tr_WDqYK6vllg/release-authorization');

        /** @var Response $response */
        $response = $client->send($request);

        $this->assertTrue($response->successful());
    }
}

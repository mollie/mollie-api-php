<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Data\DataCollection;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Data\OrderLine;
use Mollie\Api\Http\Requests\CreateSessionRequest;
use Mollie\Api\Resources\Session;
use PHPUnit\Framework\TestCase;

class CreateSessionRequestTest extends TestCase
{
    /** @test */
    public function it_can_create_session()
    {
        $client = new MockMollieClient([
            CreateSessionRequest::class => MockResponse::created('session'),
        ]);

        $lines = new DataCollection([
            new OrderLine(
                'Product A',
                1,
                new Money('EUR', '10.00'),
                new Money('EUR', '10.00')
            ),
        ]);

        $request = new CreateSessionRequest(
            new Money('EUR', '10.00'),
            'My product',
            'https://example.com/redirect',
            $lines
        );

        /** @var Session */
        $session = $client->send($request);

        $this->assertTrue($session->getResponse()->successful());
        $this->assertInstanceOf(Session::class, $session);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $lines = new DataCollection([
            new OrderLine(
                'Product A',
                1,
                new Money('EUR', '10.00'),
                new Money('EUR', '10.00')
            ),
        ]);

        $request = new CreateSessionRequest(
            new Money('EUR', '10.00'),
            'My product',
            'https://example.com/redirect',
            $lines
        );
        $this->assertEquals('sessions', $request->resolveResourcePath());
    }
}

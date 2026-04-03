<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Data\Address;
use Mollie\Api\Http\Data\DataCollection;
use Mollie\Api\Http\Data\Money;
use Mollie\Api\Http\Data\OrderLine;
use Mollie\Api\Http\Requests\UpdatePaymentLinkRequest;
use Mollie\Api\Resources\PaymentLink;
use PHPUnit\Framework\TestCase;

class UpdatePaymentLinkRequestTest extends TestCase
{
    /** @test */
    public function it_can_update_payment_link()
    {
        $client = new MockMollieClient([
            UpdatePaymentLinkRequest::class => MockResponse::ok('payment-link'),
        ]);

        $request = new UpdatePaymentLinkRequest('pl_4Y0eZitmBnQ5jsBYZIBw', 'Updated payment link');

        /** @var PaymentLink */
        $paymentLink = $client->send($request);

        $this->assertTrue($paymentLink->getResponse()->successful());
        $this->assertInstanceOf(PaymentLink::class, $paymentLink);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new UpdatePaymentLinkRequest('pl_4Y0eZitmBnQ5jsBYZIBw', 'Updated payment link');

        $this->assertEquals('payment-links/pl_4Y0eZitmBnQ5jsBYZIBw', $request->resolveResourcePath());
    }

    /** @test */
    public function it_can_update_payment_link_with_klarna_fields()
    {
        $client = new MockMollieClient([
            UpdatePaymentLinkRequest::class => MockResponse::ok('payment-link'),
        ]);

        $lines = new DataCollection([
            new OrderLine(
                'Bicycle tire',
                2,
                new Money('EUR', '12.48'),
                new Money('EUR', '24.95'),
                null,
                null,
                null,
                null,
                '21.00',
                new Money('EUR', '4.34')
            ),
        ]);

        $billing = new Address(null, 'John', 'Doe', null, 'Keizersgracht 126', null, '1015 CW', 'john.doe@example.org', null, 'Amsterdam', null, 'NL');
        $shipping = new Address(null, 'Jane', 'Doe', null, 'Herengracht 182', null, '1016 BS', 'jane.doe@example.org', null, 'Amsterdam', null, 'NL');

        $request = new UpdatePaymentLinkRequest(
            'pl_4Y0eZitmBnQ5jsBYZIBw',
            'Klarna order',
            false,
            null,
            $lines,
            $billing,
            $shipping,
            new Money('EUR', '10.00')
        );

        /** @var PaymentLink */
        $paymentLink = $client->send($request);

        $this->assertTrue($paymentLink->getResponse()->successful());
        $this->assertInstanceOf(PaymentLink::class, $paymentLink);
    }
}

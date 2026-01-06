<?php

namespace Tests\Factories;

use Mollie\Api\Factories\GetPaymentCaptureRequestFactory;
use Mollie\Api\Http\Requests\GetPaymentCaptureRequest;
use Mollie\Api\Types\PaymentIncludesQuery;
use PHPUnit\Framework\TestCase;

class GetPaymentCaptureRequestFactoryTest extends TestCase
{
    private const PAYMENT_ID = 'tr_12345';

    private const CAPTURE_ID = 'cap_12345';

    /** @test */
    public function create_returns_payment_capture_request_object_with_full_data()
    {
        $request = GetPaymentCaptureRequestFactory::new(self::PAYMENT_ID, self::CAPTURE_ID)
            ->withQuery([
                'embed' => ['payment'],
            ])
            ->create();

        $this->assertInstanceOf(GetPaymentCaptureRequest::class, $request);
        $this->assertEquals(PaymentIncludesQuery::PAYMENT, $request->query()->get('embed'));
    }

    /** @test */
    public function create_supports_legacy_include_payment_query_key()
    {
        $request = GetPaymentCaptureRequestFactory::new(self::PAYMENT_ID, self::CAPTURE_ID)
            ->withQuery([
                'includePayment' => true,
            ])
            ->create();

        $this->assertInstanceOf(GetPaymentCaptureRequest::class, $request);
        $this->assertEquals(PaymentIncludesQuery::PAYMENT, $request->query()->get('embed'));
    }

    /** @test */
    public function create_returns_payment_capture_request_object_with_minimal_data()
    {
        $request = GetPaymentCaptureRequestFactory::new(self::PAYMENT_ID, self::CAPTURE_ID)
            ->create();

        $this->assertInstanceOf(GetPaymentCaptureRequest::class, $request);
    }
}

<?php

namespace Tests\Factories;

use Mollie\Api\Factories\GetPaymentRequestFactory;
use Mollie\Api\Http\Requests\GetPaymentRequest;
use Mollie\Api\Types\PaymentQuery;
use PHPUnit\Framework\TestCase;

class GetPaymentRequestFactoryTest extends TestCase
{
    private const PAYMENT_ID = 'tr_12345';

    /** @test */
    public function create_returns_payment_request_object_with_full_data()
    {
        $request = GetPaymentRequestFactory::new(self::PAYMENT_ID)
            ->withQuery([
                'embed' => ['captures', 'refunds', 'chargebacks'],
                'include' => [PaymentQuery::INCLUDE_QR_CODE, PaymentQuery::INCLUDE_REMAINDER_DETAILS],
            ])
            ->create();

        $this->assertInstanceOf(GetPaymentRequest::class, $request);
        $this->assertEquals(PaymentQuery::EMBED_CAPTURES.','.PaymentQuery::EMBED_REFUNDS.','.PaymentQuery::EMBED_CHARGEBACKS, $request->query()->get('embed'));
        $this->assertEquals(PaymentQuery::INCLUDE_QR_CODE.','.PaymentQuery::INCLUDE_REMAINDER_DETAILS, $request->query()->get('include'));
    }

    /** @test */
    public function create_returns_payment_request_object_with_minimal_data()
    {
        $request = GetPaymentRequestFactory::new(self::PAYMENT_ID)
            ->create();

        $this->assertInstanceOf(GetPaymentRequest::class, $request);
    }

    /** @test */
    public function create_returns_payment_request_object_with_partial_data()
    {
        $request = GetPaymentRequestFactory::new(self::PAYMENT_ID)
            ->withQuery([
                'embed' => ['captures'],
                'include' => [PaymentQuery::INCLUDE_QR_CODE],
            ])
            ->create();

        $this->assertInstanceOf(GetPaymentRequest::class, $request);
        $this->assertEquals(PaymentQuery::EMBED_CAPTURES, $request->query()->get('embed'));
        $this->assertEquals(PaymentQuery::INCLUDE_QR_CODE, $request->query()->get('include'));
    }
}

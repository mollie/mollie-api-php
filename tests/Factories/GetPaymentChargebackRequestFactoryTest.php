<?php

namespace Tests\Factories;

use Mollie\Api\Factories\GetPaymentChargebackRequestFactory;
use Mollie\Api\Http\Requests\GetPaymentChargebackRequest;
use Mollie\Api\Types\PaymentIncludesQuery;
use PHPUnit\Framework\TestCase;

class GetPaymentChargebackRequestFactoryTest extends TestCase
{
    private const PAYMENT_ID = 'tr_12345';

    private const CHARGEBACK_ID = 'chb_12345';

    /** @test */
    public function create_returns_payment_chargeback_request_object_with_full_data()
    {
        $request = GetPaymentChargebackRequestFactory::new(self::PAYMENT_ID, self::CHARGEBACK_ID)
            ->withQuery([
                'include' => ['payment'],
            ])
            ->create();

        $this->assertInstanceOf(GetPaymentChargebackRequest::class, $request);
        $this->assertEquals(PaymentIncludesQuery::PAYMENT, $request->query()->get('include'));
    }

    /** @test */
    public function create_supports_legacy_include_payment_query_key()
    {
        $request = GetPaymentChargebackRequestFactory::new(self::PAYMENT_ID, self::CHARGEBACK_ID)
            ->withQuery([
                'includePayment' => true,
            ])
            ->create();

        $this->assertInstanceOf(GetPaymentChargebackRequest::class, $request);
        $this->assertEquals(PaymentIncludesQuery::PAYMENT, $request->query()->get('include'));
    }

    /** @test */
    public function create_returns_payment_chargeback_request_object_with_minimal_data()
    {
        $request = GetPaymentChargebackRequestFactory::new(self::PAYMENT_ID, self::CHARGEBACK_ID)
            ->create();

        $this->assertInstanceOf(GetPaymentChargebackRequest::class, $request);
    }
}

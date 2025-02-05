<?php

namespace Tests\Factories;

use Mollie\Api\Factories\GetPaymentMethodRequestFactory;
use Mollie\Api\Http\Requests\GetPaymentMethodRequest;
use PHPUnit\Framework\TestCase;

class GetPaymentMethodRequestFactoryTest extends TestCase
{
    private const METHOD_ID = 'ideal';

    /** @test */
    public function create_returns_payment_method_request_object_with_full_data()
    {
        $request = GetPaymentMethodRequestFactory::new(self::METHOD_ID)
            ->withQuery([
                'locale' => 'nl_NL',
                'currency' => 'EUR',
                'profileId' => 'pfl_12345',
                'include' => ['issuers', 'pricing'],
            ])
            ->create();

        $this->assertInstanceOf(GetPaymentMethodRequest::class, $request);
    }

    /** @test */
    public function create_returns_payment_method_request_object_with_minimal_data()
    {
        $request = GetPaymentMethodRequestFactory::new(self::METHOD_ID)
            ->create();

        $this->assertInstanceOf(GetPaymentMethodRequest::class, $request);
    }

    /** @test */
    public function create_returns_payment_method_request_object_with_partial_data()
    {
        $request = GetPaymentMethodRequestFactory::new(self::METHOD_ID)
            ->withQuery([
                'locale' => 'nl_NL',
                'currency' => 'EUR',
            ])
            ->create();

        $this->assertInstanceOf(GetPaymentMethodRequest::class, $request);
    }
}

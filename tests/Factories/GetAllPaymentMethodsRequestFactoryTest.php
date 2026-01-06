<?php

namespace Tests\Factories;

use Mollie\Api\Factories\GetAllPaymentMethodsRequestFactory;
use Mollie\Api\Http\Requests\GetAllMethodsRequest;
use Mollie\Api\Types\MethodQuery;
use PHPUnit\Framework\TestCase;

class GetAllPaymentMethodsRequestFactoryTest extends TestCase
{
    /** @test */
    public function create_returns_all_methods_request_object_with_full_data()
    {
        $request = GetAllPaymentMethodsRequestFactory::new()
            ->withQuery([
                'include' => ['issuers', 'pricing'],
                'locale' => 'nl_NL',
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '100.00',
                ],
                'profileId' => 'pfl_12345',
            ])
            ->create();

        $this->assertInstanceOf(GetAllMethodsRequest::class, $request);
        $this->assertEquals(MethodQuery::INCLUDE_ISSUERS.','.MethodQuery::INCLUDE_PRICING, $request->query()->get('include'));
    }

    /** @test */
    public function create_supports_legacy_include_flags()
    {
        $request = GetAllPaymentMethodsRequestFactory::new()
            ->withQuery([
                'includeIssuers' => true,
                'includePricing' => true,
            ])
            ->create();

        $this->assertInstanceOf(GetAllMethodsRequest::class, $request);
        $this->assertEquals(MethodQuery::INCLUDE_ISSUERS.','.MethodQuery::INCLUDE_PRICING, $request->query()->get('include'));
    }

    /** @test */
    public function create_returns_all_methods_request_object_with_minimal_data()
    {
        $request = GetAllPaymentMethodsRequestFactory::new()
            ->create();

        $this->assertInstanceOf(GetAllMethodsRequest::class, $request);
    }

    /** @test */
    public function create_returns_all_methods_request_object_with_partial_data()
    {
        $request = GetAllPaymentMethodsRequestFactory::new()
            ->withQuery([
                'include' => ['issuers'],
                'locale' => 'nl_NL',
            ])
            ->create();

        $this->assertInstanceOf(GetAllMethodsRequest::class, $request);
        $this->assertEquals(MethodQuery::INCLUDE_ISSUERS, $request->query()->get('include'));
    }
}

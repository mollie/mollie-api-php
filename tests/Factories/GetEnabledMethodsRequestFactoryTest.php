<?php

namespace Tests\Factories;

use Mollie\Api\Factories\GetEnabledMethodsRequestFactory;
use Mollie\Api\Http\Requests\GetEnabledMethodsRequest;
use Mollie\Api\Types\MethodQuery;
use PHPUnit\Framework\TestCase;

class GetEnabledMethodsRequestFactoryTest extends TestCase
{
    /** @test */
    public function create_returns_enabled_methods_request_object_with_full_data()
    {
        $request = GetEnabledMethodsRequestFactory::new()
            ->withQuery([
                'sequenceType' => 'oneoff',
                'resource' => 'payments',
                'locale' => 'nl_NL',
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '100.00',
                ],
                'billingCountry' => 'NL',
                'includeWallets' => ['applepay'],
                'orderLineCategories' => ['digital-goods', 'physical-goods'],
                'profileId' => 'pfl_12345',
                'include' => ['issuers', 'pricing'],
            ])
            ->create();

        $this->assertInstanceOf(GetEnabledMethodsRequest::class, $request);
        $this->assertEquals(MethodQuery::INCLUDE_ISSUERS, $request->query()->get('include'));
    }

    /** @test */
    public function create_supports_legacy_include_flags()
    {
        $request = GetEnabledMethodsRequestFactory::new()
            ->withQuery([
                'includeIssuers' => true,
                'includePricing' => true,
            ])
            ->create();

        $this->assertInstanceOf(GetEnabledMethodsRequest::class, $request);
        $this->assertEquals(MethodQuery::INCLUDE_ISSUERS, $request->query()->get('include'));
    }

    /** @test */
    public function create_returns_enabled_methods_request_object_with_minimal_data()
    {
        $request = GetEnabledMethodsRequestFactory::new()
            ->create();

        $this->assertInstanceOf(GetEnabledMethodsRequest::class, $request);
    }

    /** @test */
    public function create_returns_enabled_methods_request_object_with_partial_data()
    {
        $request = GetEnabledMethodsRequestFactory::new()
            ->withQuery([
                'locale' => 'nl_NL',
                'amount' => [
                    'currency' => 'EUR',
                    'value' => '100.00',
                ],
                'billingCountry' => 'NL',
            ])
            ->create();

        $this->assertInstanceOf(GetEnabledMethodsRequest::class, $request);
    }
}

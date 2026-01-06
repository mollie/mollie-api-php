<?php

namespace Tests\Factories;

use Mollie\Api\Factories\GetMethodRequestFactory;
use Mollie\Api\Http\Requests\GetMethodRequest;
use Mollie\Api\Types\MethodQuery;
use PHPUnit\Framework\TestCase;

class GetMethodRequestFactoryTest extends TestCase
{
    private const METHOD_ID = 'ideal';

    /** @test */
    public function create_returns_payment_method_request_object_with_full_data()
    {
        $request = GetMethodRequestFactory::new(self::METHOD_ID)
            ->withQuery([
                'locale' => 'nl_NL',
                'currency' => 'EUR',
                'profileId' => 'pfl_12345',
                'include' => ['issuers', 'pricing'],
            ])
            ->create();

        $this->assertInstanceOf(GetMethodRequest::class, $request);
        $this->assertEquals(MethodQuery::INCLUDE_ISSUERS, $request->query()->get('include'));
    }

    /** @test */
    public function create_supports_legacy_include_flags()
    {
        $request = GetMethodRequestFactory::new(self::METHOD_ID)
            ->withQuery([
                'includeIssuers' => true,
                'includePricing' => true,
            ])
            ->create();

        $this->assertInstanceOf(GetMethodRequest::class, $request);
        $this->assertEquals(MethodQuery::INCLUDE_ISSUERS, $request->query()->get('include'));
    }

    /** @test */
    public function create_returns_payment_method_request_object_with_minimal_data()
    {
        $request = GetMethodRequestFactory::new(self::METHOD_ID)
            ->create();

        $this->assertInstanceOf(GetMethodRequest::class, $request);
    }

    /** @test */
    public function create_returns_payment_method_request_object_with_partial_data()
    {
        $request = GetMethodRequestFactory::new(self::METHOD_ID)
            ->withQuery([
                'locale' => 'nl_NL',
                'currency' => 'EUR',
            ])
            ->create();

        $this->assertInstanceOf(GetMethodRequest::class, $request);
    }
}

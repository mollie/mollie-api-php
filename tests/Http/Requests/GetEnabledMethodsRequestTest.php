<?php

namespace Tests\Http\Requests;

use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Fake\MockResponse;
use Mollie\Api\Http\Requests\GetEnabledMethodsRequest;
use Mollie\Api\Resources\Method;
use Mollie\Api\Resources\MethodCollection;
use PHPUnit\Framework\TestCase;

class GetEnabledMethodsRequestTest extends TestCase
{
    /** @test */
    public function it_can_get_enabled_methods()
    {
        $client = new MockMollieClient([
            GetEnabledMethodsRequest::class => MockResponse::ok('method-list'),
        ]);

        $request = new GetEnabledMethodsRequest;

        /** @var MethodCollection */
        $methods = $client->send($request);

        $this->assertTrue($methods->getResponse()->successful());
        $this->assertInstanceOf(MethodCollection::class, $methods);
    }

    /** @test */
    public function it_resolves_correct_resource_path()
    {
        $request = new GetEnabledMethodsRequest;

        $this->assertEquals('methods', $request->resolveResourcePath());
    }

    /** @test */
    public function it_filters_out_methods_with_null_status_when_flag_is_true()
    {
        $client = new MockMollieClient([
            GetEnabledMethodsRequest::class => MockResponse::list(MethodCollection::class)
                ->addMany($this->getMethodListResponse())
                ->create(),
        ]);

        $request = new GetEnabledMethodsRequest;

        /** @var MethodCollection */
        $methods = $client->send($request);

        // Assert that we have the right number of methods (only the active and inactive ones)
        $this->assertCount(2, $methods);

        // Verify that all methods have non-null status
        foreach ($methods as $method) {
            $this->assertNotNull($method->status);
        }

        // Verify that methods with active and inactive status are included
        $methodIds = array_map(function (Method $method) {
            return $method->id;
        }, iterator_to_array($methods));

        $this->assertContains('ideal', $methodIds);
        $this->assertContains('creditcard', $methodIds);
        $this->assertNotContains('voucher', $methodIds);
    }

    /** @test */
    public function it_does_not_filter_out_methods_with_null_status_when_flag_is_false()
    {
        $client = new MockMollieClient([
            GetEnabledMethodsRequest::class => MockResponse::list(MethodCollection::class)
                ->addMany($this->getMethodListResponse())
                ->create(),
        ]);

        // Create a request with filtersNullStatus = false
        $request = new GetEnabledMethodsRequest;

        $request->withNullStatus();

        /** @var MethodCollection */
        $methods = $client->send($request);

        // Assert that we have all methods (active, inactive, and null status)
        $this->assertCount(3, $methods);

        // Verify that methods with all statuses are included
        $methodIds = array_map(function (Method $method) {
            return $method->id;
        }, iterator_to_array($methods));

        $this->assertContains('ideal', $methodIds);
        $this->assertContains('creditcard', $methodIds);
        $this->assertContains('voucher', $methodIds);
    }

    /**
     * Create a mock response with methods having different statuses
     */
    private function getMethodListResponse(): array
    {
        return [
            [
                'resource' => 'method',
                'id' => 'ideal',
                'description' => 'iDEAL',
                'status' => 'active',
                'image' => [
                    'size1x' => 'https://www.mollie.com/external/icons/payment-methods/ideal.png',
                    'size2x' => 'https://www.mollie.com/external/icons/payment-methods/ideal%402x.png',
                    'svg' => 'https://www.mollie.com/external/icons/payment-methods/ideal.svg',
                ],
            ],
            [
                'resource' => 'method',
                'id' => 'creditcard',
                'description' => 'Credit card',
                'status' => 'inactive',
                'image' => [
                    'size1x' => 'https://www.mollie.com/external/icons/payment-methods/creditcard.png',
                    'size2x' => 'https://www.mollie.com/external/icons/payment-methods/creditcard%402x.png',
                    'svg' => 'https://www.mollie.com/external/icons/payment-methods/creditcard.svg',
                ],
            ],
            [
                'resource' => 'method',
                'id' => 'voucher',
                'description' => 'Voucher',
                'status' => null,
                'image' => [
                    'size1x' => 'https://www.mollie.com/external/icons/payment-methods/voucher.png',
                    'size2x' => 'https://www.mollie.com/external/icons/payment-methods/voucher%402x.png',
                    'svg' => 'https://www.mollie.com/external/icons/payment-methods/voucher.svg',
                ],
            ],
        ];
    }
}

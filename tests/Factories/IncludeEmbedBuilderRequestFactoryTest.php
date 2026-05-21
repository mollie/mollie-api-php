<?php

declare(strict_types=1);

namespace Tests\Factories;

use Mollie\Api\Factories\GetAllPaymentMethodsRequestFactory;
use Mollie\Api\Factories\GetClientRequestFactory;
use Mollie\Api\Factories\GetPaginatedRefundsRequestFactory;
use Mollie\Api\Factories\GetPaymentCaptureRequestFactory;
use Mollie\Api\Factories\GetPaymentRequestFactory;
use Mollie\Api\Types\Includes\CaptureEmbeds;
use Mollie\Api\Types\Includes\ClientEmbed;
use Mollie\Api\Types\Includes\ClientEmbeds;
use Mollie\Api\Types\Includes\MethodInclude;
use Mollie\Api\Types\Includes\MethodIncludes;
use Mollie\Api\Types\Includes\PaymentEmbed;
use Mollie\Api\Types\Includes\PaymentEmbeds;
use Mollie\Api\Types\Includes\PaymentInclude;
use Mollie\Api\Types\Includes\PaymentIncludes;
use Mollie\Api\Types\Includes\RefundEmbeds;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class IncludeEmbedBuilderRequestFactoryTest extends TestCase
{
    #[Test]
    public function payment_request_factory_accepts_include_and_embed_builders()
    {
        $request = GetPaymentRequestFactory::new('tr_123')
            ->withQuery([
                'include' => PaymentIncludes::qrCode()->remainderDetails(),
                'embed' => PaymentEmbeds::captures()->refunds(),
            ])
            ->create();

        $this->assertSame('details.qrCode,details.remainderDetails', $request->query()->get('include'));
        $this->assertSame('captures,refunds', $request->query()->get('embed'));
    }

    #[Test]
    public function payment_request_factory_accepts_raw_comma_strings_and_enum_arrays()
    {
        $request = GetPaymentRequestFactory::new('tr_123')
            ->withQuery([
                'include' => 'details.qrCode,details.remainderDetails',
                'embed' => [PaymentEmbed::Captures, PaymentEmbed::Chargebacks],
            ])
            ->create();

        $this->assertSame('details.qrCode,details.remainderDetails', $request->query()->get('include'));
        $this->assertSame('captures,chargebacks', $request->query()->get('embed'));
    }

    #[Test]
    public function method_request_factory_accepts_include_builders_and_enum_arrays()
    {
        $builderRequest = GetAllPaymentMethodsRequestFactory::new()
            ->withQuery(['include' => MethodIncludes::issuers()->pricing()])
            ->create();

        $enumRequest = GetAllPaymentMethodsRequestFactory::new()
            ->withQuery(['include' => [MethodInclude::Issuers, MethodInclude::Pricing]])
            ->create();

        $this->assertSame('issuers,pricing', $builderRequest->query()->get('include'));
        $this->assertSame('issuers,pricing', $enumRequest->query()->get('include'));
    }

    #[Test]
    public function client_request_factory_accepts_embed_builders_and_enum_arrays()
    {
        $builderRequest = GetClientRequestFactory::new('org_123')
            ->withQuery(['embed' => ClientEmbeds::organization()->onboarding()])
            ->create();

        $enumRequest = GetClientRequestFactory::new('org_123')
            ->withQuery(['embed' => [ClientEmbed::Organization, ClientEmbed::Onboarding]])
            ->create();

        $this->assertSame('organization,onboarding', $builderRequest->query()->get('embed'));
        $this->assertSame('organization,onboarding', $enumRequest->query()->get('embed'));
    }

    #[Test]
    public function child_resource_factories_accept_embed_builders()
    {
        $refundsRequest = GetPaginatedRefundsRequestFactory::new()
            ->withQuery(['embed' => RefundEmbeds::payment()])
            ->create();

        $captureRequest = GetPaymentCaptureRequestFactory::new('tr_123', 'cpt_123')
            ->withQuery(['embed' => CaptureEmbeds::payment()])
            ->create();

        $this->assertSame('payment', $refundsRequest->query()->get('embed'));
        $this->assertSame('payment', $captureRequest->query()->get('embed'));
    }

    #[Test]
    public function legacy_boolean_aliases_still_work()
    {
        $paymentRequest = GetPaymentRequestFactory::new('tr_123')
            ->withQuery([
                'includeQrCode' => true,
                'embedCaptures' => true,
            ])
            ->create();

        $captureRequest = GetPaymentCaptureRequestFactory::new('tr_123', 'cpt_123')
            ->withQuery(['includePayment' => true])
            ->create();

        $this->assertSame('details.qrCode', $paymentRequest->query()->get('include'));
        $this->assertSame('captures', $paymentRequest->query()->get('embed'));
        $this->assertSame('payment', $captureRequest->query()->get('embed'));
    }

    #[Test]
    public function enum_values_can_be_mixed_with_raw_strings()
    {
        $request = GetPaymentRequestFactory::new('tr_123')
            ->withQuery([
                'include' => [PaymentInclude::QrCode, 'details.remainderDetails'],
            ])
            ->create();

        $this->assertSame('details.qrCode,details.remainderDetails', $request->query()->get('include'));
    }
}

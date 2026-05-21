<?php

declare(strict_types=1);

namespace Tests\Types\Includes;

use BadMethodCallException;
use Mollie\Api\Types\Includes\CaptureEmbed;
use Mollie\Api\Types\Includes\CaptureEmbeds;
use Mollie\Api\Types\Includes\ChargebackEmbed;
use Mollie\Api\Types\Includes\ChargebackEmbeds;
use Mollie\Api\Types\Includes\ClientEmbed;
use Mollie\Api\Types\Includes\ClientEmbeds;
use Mollie\Api\Types\Includes\MethodInclude;
use Mollie\Api\Types\Includes\MethodIncludes;
use Mollie\Api\Types\Includes\PaymentEmbed;
use Mollie\Api\Types\Includes\PaymentEmbeds;
use Mollie\Api\Types\Includes\PaymentInclude;
use Mollie\Api\Types\Includes\PaymentIncludes;
use Mollie\Api\Types\Includes\RefundEmbed;
use Mollie\Api\Types\Includes\RefundEmbeds;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class QueryParameterBuilderTest extends TestCase
{
    #[Test]
    public function payment_includes_serialize_to_mollie_include_values()
    {
        $includes = PaymentIncludes::qrCode()->remainderDetails()->qrCode();

        $this->assertSame('details.qrCode,details.remainderDetails', $includes->toQueryValue());
        $this->assertSame('details.qrCode,details.remainderDetails', (string) $includes);
    }

    #[Test]
    public function payment_embeds_serialize_to_mollie_embed_values()
    {
        $this->assertSame(
            'captures,refunds,chargebacks',
            PaymentEmbeds::captures()->refunds()->chargebacks()->captures()->toQueryValue()
        );
    }

    #[Test]
    public function method_includes_serialize_to_mollie_include_values()
    {
        $this->assertSame('issuers,pricing', MethodIncludes::issuers()->pricing()->toQueryValue());
    }

    #[Test]
    public function client_embeds_serialize_to_mollie_embed_values()
    {
        $this->assertSame('organization,onboarding', ClientEmbeds::organization()->onboarding()->toQueryValue());
    }

    #[Test]
    public function child_resource_embeds_serialize_to_payment()
    {
        $this->assertSame('payment', RefundEmbeds::payment()->toQueryValue());
        $this->assertSame('payment', CaptureEmbeds::payment()->toQueryValue());
        $this->assertSame('payment', ChargebackEmbeds::payment()->toQueryValue());
    }

    #[Test]
    public function builders_can_be_created_from_enum_values()
    {
        $this->assertSame('details.qrCode', PaymentIncludes::from(PaymentInclude::QrCode)->toQueryValue());
        $this->assertSame('captures', PaymentEmbeds::from(PaymentEmbed::Captures)->toQueryValue());
        $this->assertSame('issuers', MethodIncludes::from(MethodInclude::Issuers)->toQueryValue());
        $this->assertSame('organization', ClientEmbeds::from(ClientEmbed::Organization)->toQueryValue());
        $this->assertSame('payment', RefundEmbeds::from(RefundEmbed::Payment)->toQueryValue());
        $this->assertSame('payment', CaptureEmbeds::from(CaptureEmbed::Payment)->toQueryValue());
        $this->assertSame('payment', ChargebackEmbeds::from(ChargebackEmbed::Payment)->toQueryValue());
    }

    #[Test]
    public function unknown_builder_methods_throw()
    {
        $this->expectException(BadMethodCallException::class);

        PaymentIncludes::qrCode()->__call('customer', []);
    }
}

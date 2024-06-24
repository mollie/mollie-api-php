<?php

namespace Tests\Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Types\PaymentStatus;
use Mollie\Api\Types\SequenceType;
use stdClass;

class PaymentTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param string $status
     * @param string $function
     * @param bool $expected_boolean
     *
     * @dataProvider dpTestPaymentStatuses
     */
    public function testPaymentStatuses($status, $function, $expected_boolean)
    {
        $payment = new Payment($this->createMock(MollieApiClient::class));
        $payment->status = $status;

        $this->assertEquals($expected_boolean, $payment->{$function}());
    }

    public function dpTestPaymentStatuses()
    {
        return [
            [PaymentStatus::PENDING, "isPending", true],
            [PaymentStatus::PENDING, "isAuthorized", false],
            [PaymentStatus::PENDING, "isFailed", false],
            [PaymentStatus::PENDING, "isOpen", false],
            [PaymentStatus::PENDING, "isCanceled", false],
            [PaymentStatus::PENDING, "isPaid", false],
            [PaymentStatus::PENDING, "isExpired", false],

            [PaymentStatus::AUTHORIZED, "isPending", false],
            [PaymentStatus::AUTHORIZED, "isAuthorized", true],
            [PaymentStatus::AUTHORIZED, "isFailed", false],
            [PaymentStatus::AUTHORIZED, "isOpen", false],
            [PaymentStatus::AUTHORIZED, "isCanceled", false],
            [PaymentStatus::AUTHORIZED, "isPaid", false],
            [PaymentStatus::AUTHORIZED, "isExpired", false],

            [PaymentStatus::FAILED, "isPending", false],
            [PaymentStatus::FAILED, "isAuthorized", false],
            [PaymentStatus::FAILED, "isFailed", true],
            [PaymentStatus::FAILED, "isOpen", false],
            [PaymentStatus::FAILED, "isCanceled", false],
            [PaymentStatus::FAILED, "isPaid", false],
            [PaymentStatus::FAILED, "isExpired", false],

            [PaymentStatus::OPEN, "isPending", false],
            [PaymentStatus::OPEN, "isAuthorized", false],
            [PaymentStatus::OPEN, "isFailed", false],
            [PaymentStatus::OPEN, "isOpen", true],
            [PaymentStatus::OPEN, "isCanceled", false],
            [PaymentStatus::OPEN, "isPaid", false],
            [PaymentStatus::OPEN, "isExpired", false],

            [PaymentStatus::CANCELED, "isPending", false],
            [PaymentStatus::CANCELED, "isAuthorized", false],
            [PaymentStatus::CANCELED, "isFailed", false],
            [PaymentStatus::CANCELED, "isOpen", false],
            [PaymentStatus::CANCELED, "isCanceled", true],
            [PaymentStatus::CANCELED, "isPaid", false],
            [PaymentStatus::CANCELED, "isExpired", false],

            [PaymentStatus::EXPIRED, "isPending", false],
            [PaymentStatus::EXPIRED, "isAuthorized", false],
            [PaymentStatus::EXPIRED, "isFailed", false],
            [PaymentStatus::EXPIRED, "isOpen", false],
            [PaymentStatus::EXPIRED, "isCanceled", false],
            [PaymentStatus::EXPIRED, "isPaid", false],
            [PaymentStatus::EXPIRED, "isExpired", true],
        ];
    }

    public function testIsPaidReturnsTrueWhenPaidDatetimeIsSet()
    {
        $payment = new Payment($this->createMock(MollieApiClient::class));

        $payment->paidAt = "2016-10-24";
        $this->assertTrue($payment->isPaid());
    }

    public function testHasRefundsReturnsTrueWhenPaymentHasRefunds()
    {
        $payment = new Payment($this->createMock(MollieApiClient::class));

        $payment->_links = new stdClass();
        $payment->_links->refunds = (object) ["href" => "https://api.mollie.com/v2/payments/tr_44aKxzEbr8/refunds", "type" => "application/hal+json"];

        $this->assertTrue($payment->hasRefunds());
    }

    public function testHasRefundsReturnsFalseWhenPaymentHasNoRefunds()
    {
        $payment = new Payment($this->createMock(MollieApiClient::class));

        $payment->_links = new stdClass();
        $this->assertFalse($payment->hasRefunds());
    }

    public function testHasChargebacksReturnsTrueWhenPaymentHasChargebacks()
    {
        $payment = new Payment($this->createMock(MollieApiClient::class));

        $payment->_links = new stdClass();
        $payment->_links->chargebacks = (object) ["href" => "https://api.mollie.com/v2/payments/tr_44aKxzEbr8/chargebacks", "type" => "application/hal+json"];

        $this->assertTrue($payment->hasChargebacks());
    }

    public function testHasChargebacksReturnsFalseWhenPaymentHasNoChargebacks()
    {
        $payment = new Payment($this->createMock(MollieApiClient::class));

        $payment->_links = new stdClass();
        $this->assertFalse($payment->hasChargebacks());
    }

    public function testHasRecurringTypeReturnsTrueWhenRecurringTypeIsFirst()
    {
        $payment = new Payment($this->createMock(MollieApiClient::class));

        $payment->sequenceType = SequenceType::FIRST;
        $this->assertFalse($payment->hasSequenceTypeRecurring());
        $this->assertTrue($payment->hasSequenceTypeFirst());
    }

    public function testHasRecurringTypeReturnsTrueWhenRecurringTypeIsRecurring()
    {
        $payment = new Payment($this->createMock(MollieApiClient::class));

        $payment->sequenceType = SequenceType::RECURRING;
        $this->assertTrue($payment->hasSequenceTypeRecurring());
        $this->assertFalse($payment->hasSequenceTypeFirst());
    }

    public function testHasRecurringTypeReturnsFalseWhenRecurringTypeIsNone()
    {
        $payment = new Payment($this->createMock(MollieApiClient::class));

        $payment->sequenceType = SequenceType::ONEOFF;
        $this->assertFalse($payment->hasSequenceTypeFirst());
        $this->assertFalse($payment->hasSequenceTypeRecurring());
    }

    public function testGetCheckoutUrlReturnsPaymentUrlFromLinksObject()
    {
        $payment = new Payment($this->createMock(MollieApiClient::class));

        $payment->_links = new stdClass();
        $payment->_links->checkout = new stdClass();
        $payment->_links->checkout->href = "https://example.com";

        $this->assertSame($payment->getCheckoutUrl(), "https://example.com");
    }

    public function testGetMobileAppCheckoutUrlReturnsPaymentUrlFromLinksObject()
    {
        $payment = new Payment($this->createMock(MollieApiClient::class));

        $payment->_links = new stdClass();
        $payment->_links->mobileAppCheckout = new stdClass();
        $payment->_links->mobileAppCheckout->href = "https://example-mobile-checkout.com";


        $this->assertSame($payment->getMobileAppCheckoutUrl(), "https://example-mobile-checkout.com");
    }

    public function testCanBeRefundedReturnsTrueWhenAmountRemainingIsSet()
    {
        $payment = new Payment($this->createMock(MollieApiClient::class));

        $amountRemaining = new Stdclass();
        $amountRemaining->value = '15.00';
        $amountRemaining->currency = "EUR";

        $payment->amountRemaining = $amountRemaining;
        $this->assertTrue($payment->canBeRefunded());
        $this->assertTrue($payment->canBePartiallyRefunded());
    }

    public function testCanBeRefundedReturnsFalseWhenAmountRemainingIsNull()
    {
        $payment = new Payment($this->createMock(MollieApiClient::class));

        $payment->amountRemaining = null;
        $this->assertFalse($payment->canBeRefunded());
        $this->assertFalse($payment->canBePartiallyRefunded());
    }

    public function testGetAmountRefundedReturnsAmountRefundedAsFloat()
    {
        $payment = new Payment($this->createMock(MollieApiClient::class));

        $payment->amountRefunded = (object)["value" => 22.0, "currency" => "EUR"];
        self::assertSame(22.0, $payment->getAmountRefunded());
    }

    public function testGetAmountRefundedReturns0WhenAmountRefundedIsSetToNull()
    {
        $payment = new Payment($this->createMock(MollieApiClient::class));

        $payment->amountRefunded = null;
        self::assertSame(0.0, $payment->getAmountRefunded());
    }

    public function testGetAmountRemainingReturnsAmountRemainingAsFloat()
    {
        $payment = new Payment($this->createMock(MollieApiClient::class));

        $payment->amountRemaining = (object)["value" => 22.0, "currency" => "EUR"];
        self::assertSame(22.0, $payment->getAmountRemaining());
    }

    public function testGetAmountRemainingReturns0WhenAmountRemainingIsSetToNull()
    {
        $payment = new Payment($this->createMock(MollieApiClient::class));

        $payment->amountRefunded = null;
        self::assertSame(0.0, $payment->getAmountRemaining());
    }

    public function testGetAmountChargedBackReturnsAmountChargedBackAsFloat()
    {
        $payment = new Payment($this->createMock(MollieApiClient::class));

        $payment->amountChargedBack = (object)["value" => 22.0, "currency" => "EUR"];
        self::assertSame(22.0, $payment->getAmountChargedBack());
    }

    public function testGetAmountChargedBackReturns0WhenAmountChargedBackIsSetToNull()
    {
        $payment = new Payment($this->createMock(MollieApiClient::class));

        $payment->amountChargedBack = null;
        self::assertSame(0.0, $payment->getAmountChargedBack());
    }

    public function testGetSettlementAmountReturns0WhenSettlementAmountIsSetToNull()
    {
        $payment = new Payment($this->createMock(MollieApiClient::class));

        $payment->settlementAmount = null;
        self::assertSame(0.0, $payment->getSettlementAmount());
    }

    public function testGetSettlementAmountReturnsSettlementAmountAsFloat()
    {
        $payment = new Payment($this->createMock(MollieApiClient::class));

        $payment->settlementAmount = (object)["value" => 22.0, "currency" => "EUR"];
        self::assertSame(22.0, $payment->getSettlementAmount());
    }

    public function testHasSplitPaymentsReturnsFalseWhenPaymentHasNoSplit()
    {
        $payment = new Payment($this->createMock(MollieApiClient::class));

        $payment->_links = new stdClass();
        $this->assertFalse($payment->hasSplitPayments());
    }
}

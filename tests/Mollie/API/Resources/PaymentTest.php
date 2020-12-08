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
            [PaymentStatus::STATUS_PENDING, "isPending", true],
            [PaymentStatus::STATUS_PENDING, "isAuthorized", false],
            [PaymentStatus::STATUS_PENDING, "isFailed", false],
            [PaymentStatus::STATUS_PENDING, "isOpen", false],
            [PaymentStatus::STATUS_PENDING, "isCanceled", false],
            [PaymentStatus::STATUS_PENDING, "isPaid", false],
            [PaymentStatus::STATUS_PENDING, "isExpired", false],

            [PaymentStatus::STATUS_AUTHORIZED, "isPending", false],
            [PaymentStatus::STATUS_AUTHORIZED, "isAuthorized", true],
            [PaymentStatus::STATUS_AUTHORIZED, "isFailed", false],
            [PaymentStatus::STATUS_AUTHORIZED, "isOpen", false],
            [PaymentStatus::STATUS_AUTHORIZED, "isCanceled", false],
            [PaymentStatus::STATUS_AUTHORIZED, "isPaid", false],
            [PaymentStatus::STATUS_AUTHORIZED, "isExpired", false],

            [PaymentStatus::STATUS_FAILED, "isPending", false],
            [PaymentStatus::STATUS_FAILED, "isAuthorized", false],
            [PaymentStatus::STATUS_FAILED, "isFailed", true],
            [PaymentStatus::STATUS_FAILED, "isOpen", false],
            [PaymentStatus::STATUS_FAILED, "isCanceled", false],
            [PaymentStatus::STATUS_FAILED, "isPaid", false],
            [PaymentStatus::STATUS_FAILED, "isExpired", false],

            [PaymentStatus::STATUS_OPEN, "isPending", false],
            [PaymentStatus::STATUS_OPEN, "isAuthorized", false],
            [PaymentStatus::STATUS_OPEN, "isFailed", false],
            [PaymentStatus::STATUS_OPEN, "isOpen", true],
            [PaymentStatus::STATUS_OPEN, "isCanceled", false],
            [PaymentStatus::STATUS_OPEN, "isPaid", false],
            [PaymentStatus::STATUS_OPEN, "isExpired", false],

            [PaymentStatus::STATUS_CANCELED, "isPending", false],
            [PaymentStatus::STATUS_CANCELED, "isAuthorized", false],
            [PaymentStatus::STATUS_CANCELED, "isFailed", false],
            [PaymentStatus::STATUS_CANCELED, "isOpen", false],
            [PaymentStatus::STATUS_CANCELED, "isCanceled", true],
            [PaymentStatus::STATUS_CANCELED, "isPaid", false],
            [PaymentStatus::STATUS_CANCELED, "isExpired", false],

            [PaymentStatus::STATUS_EXPIRED, "isPending", false],
            [PaymentStatus::STATUS_EXPIRED, "isAuthorized", false],
            [PaymentStatus::STATUS_EXPIRED, "isFailed", false],
            [PaymentStatus::STATUS_EXPIRED, "isOpen", false],
            [PaymentStatus::STATUS_EXPIRED, "isCanceled", false],
            [PaymentStatus::STATUS_EXPIRED, "isPaid", false],
            [PaymentStatus::STATUS_EXPIRED, "isExpired", true],
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

        $payment->sequenceType = SequenceType::SEQUENCETYPE_FIRST;
        $this->assertFalse($payment->hasSequenceTypeRecurring());
        $this->assertTrue($payment->hasSequenceTypeFirst());
    }

    public function testHasRecurringTypeReturnsTrueWhenRecurringTypeIsRecurring()
    {
        $payment = new Payment($this->createMock(MollieApiClient::class));

        $payment->sequenceType = SequenceType::SEQUENCETYPE_RECURRING;
        $this->assertTrue($payment->hasSequenceTypeRecurring());
        $this->assertFalse($payment->hasSequenceTypeFirst());
    }

    public function testHasRecurringTypeReturnsFalseWhenRecurringTypeIsNone()
    {
        $payment = new Payment($this->createMock(MollieApiClient::class));

        $payment->sequenceType = SequenceType::SEQUENCETYPE_ONEOFF;
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

    public function testCanBeRefundedReturnsTrueWhenAmountRemainingIsSet()
    {
        $payment = new Payment($this->createMock(MollieApiClient::class));

        $payment->amountRemaining = 15;
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
}

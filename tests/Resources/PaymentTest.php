<?php

declare(strict_types=1);

namespace Tests\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Payment;
use Mollie\Api\Types\PaymentStatus;
use Mollie\Api\Types\SequenceType;
use stdClass;

class PaymentTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param  string  $status
     * @param  string  $function
     * @param  bool  $expected_boolean
     *
     * @dataProvider dpTestPaymentStatuses
     */
    public function test_payment_statuses($status, $function, $expected_boolean)
    {
        $payment = new Payment(
            $this->createMock(MollieApiClient::class),
        );
        $payment->status = $status;

        $this->assertEquals($expected_boolean, $payment->{$function}());
    }

    public function dpTestPaymentStatuses()
    {
        return [
            [PaymentStatus::Pending->value, 'isPending', true],
            [PaymentStatus::Pending->value, 'isAuthorized', false],
            [PaymentStatus::Pending->value, 'isFailed', false],
            [PaymentStatus::Pending->value, 'isOpen', false],
            [PaymentStatus::Pending->value, 'isCanceled', false],
            [PaymentStatus::Pending->value, 'isPaid', false],
            [PaymentStatus::Pending->value, 'isExpired', false],

            [PaymentStatus::Authorized->value, 'isPending', false],
            [PaymentStatus::Authorized->value, 'isAuthorized', true],
            [PaymentStatus::Authorized->value, 'isFailed', false],
            [PaymentStatus::Authorized->value, 'isOpen', false],
            [PaymentStatus::Authorized->value, 'isCanceled', false],
            [PaymentStatus::Authorized->value, 'isPaid', false],
            [PaymentStatus::Authorized->value, 'isExpired', false],

            [PaymentStatus::Failed->value, 'isPending', false],
            [PaymentStatus::Failed->value, 'isAuthorized', false],
            [PaymentStatus::Failed->value, 'isFailed', true],
            [PaymentStatus::Failed->value, 'isOpen', false],
            [PaymentStatus::Failed->value, 'isCanceled', false],
            [PaymentStatus::Failed->value, 'isPaid', false],
            [PaymentStatus::Failed->value, 'isExpired', false],

            [PaymentStatus::Open->value, 'isPending', false],
            [PaymentStatus::Open->value, 'isAuthorized', false],
            [PaymentStatus::Open->value, 'isFailed', false],
            [PaymentStatus::Open->value, 'isOpen', true],
            [PaymentStatus::Open->value, 'isCanceled', false],
            [PaymentStatus::Open->value, 'isPaid', false],
            [PaymentStatus::Open->value, 'isExpired', false],

            [PaymentStatus::Canceled->value, 'isPending', false],
            [PaymentStatus::Canceled->value, 'isAuthorized', false],
            [PaymentStatus::Canceled->value, 'isFailed', false],
            [PaymentStatus::Canceled->value, 'isOpen', false],
            [PaymentStatus::Canceled->value, 'isCanceled', true],
            [PaymentStatus::Canceled->value, 'isPaid', false],
            [PaymentStatus::Canceled->value, 'isExpired', false],

            [PaymentStatus::Expired->value, 'isPending', false],
            [PaymentStatus::Expired->value, 'isAuthorized', false],
            [PaymentStatus::Expired->value, 'isFailed', false],
            [PaymentStatus::Expired->value, 'isOpen', false],
            [PaymentStatus::Expired->value, 'isCanceled', false],
            [PaymentStatus::Expired->value, 'isPaid', false],
            [PaymentStatus::Expired->value, 'isExpired', true],
        ];
    }

    public function test_is_paid_returns_true_when_paid_datetime_is_set()
    {
        $payment = new Payment(
            $this->createMock(MollieApiClient::class),
        );

        $payment->paidAt = '2016-10-24';
        $this->assertTrue($payment->isPaid());
    }

    public function test_has_refunds_returns_true_when_payment_has_refunds()
    {
        $payment = new Payment(
            $this->createMock(MollieApiClient::class),
        );

        $payment->_links = new stdClass;
        $payment->_links->refunds = (object) ['href' => 'https://api.mollie.com/v2/payments/tr_44aKxzEbr8/refunds', 'type' => 'application/hal+json'];

        $this->assertTrue($payment->hasRefunds());
    }

    public function test_has_refunds_returns_false_when_payment_has_no_refunds()
    {
        $payment = new Payment(
            $this->createMock(MollieApiClient::class),
        );

        $payment->_links = new stdClass;
        $this->assertFalse($payment->hasRefunds());
    }

    public function test_has_chargebacks_returns_true_when_payment_has_chargebacks()
    {
        $payment = new Payment(
            $this->createMock(MollieApiClient::class),
        );

        $payment->_links = new stdClass;
        $payment->_links->chargebacks = (object) ['href' => 'https://api.mollie.com/v2/payments/tr_44aKxzEbr8/chargebacks', 'type' => 'application/hal+json'];

        $this->assertTrue($payment->hasChargebacks());
    }

    public function test_has_chargebacks_returns_false_when_payment_has_no_chargebacks()
    {
        $payment = new Payment(
            $this->createMock(MollieApiClient::class),
        );

        $payment->_links = new stdClass;
        $this->assertFalse($payment->hasChargebacks());
    }

    public function test_has_recurring_type_returns_true_when_recurring_type_is_first()
    {
        $payment = new Payment(
            $this->createMock(MollieApiClient::class),
        );

        $payment->sequenceType = SequenceType::First->value;
        $this->assertFalse($payment->hasSequenceTypeRecurring());
        $this->assertTrue($payment->hasSequenceTypeFirst());
    }

    public function test_has_recurring_type_returns_true_when_recurring_type_is_recurring()
    {
        $payment = new Payment(
            $this->createMock(MollieApiClient::class),
        );

        $payment->sequenceType = SequenceType::Recurring->value;
        $this->assertTrue($payment->hasSequenceTypeRecurring());
        $this->assertFalse($payment->hasSequenceTypeFirst());
    }

    public function test_has_recurring_type_returns_false_when_recurring_type_is_none()
    {
        $payment = new Payment(
            $this->createMock(MollieApiClient::class),
        );

        $payment->sequenceType = SequenceType::Oneoff->value;
        $this->assertFalse($payment->hasSequenceTypeFirst());
        $this->assertFalse($payment->hasSequenceTypeRecurring());
    }

    public function test_get_checkout_url_returns_payment_url_from_links_object()
    {
        $payment = new Payment(
            $this->createMock(MollieApiClient::class),
        );

        $payment->_links = new stdClass;
        $payment->_links->checkout = new stdClass;
        $payment->_links->checkout->href = 'https://example.com';

        $this->assertSame($payment->getCheckoutUrl(), 'https://example.com');
    }

    public function test_get_mobile_app_checkout_url_returns_payment_url_from_links_object()
    {
        $payment = new Payment(
            $this->createMock(MollieApiClient::class),
        );

        $payment->_links = new stdClass;
        $payment->_links->mobileAppCheckout = new stdClass;
        $payment->_links->mobileAppCheckout->href = 'https://example-mobile-checkout.com';

        $this->assertSame($payment->getMobileAppCheckoutUrl(), 'https://example-mobile-checkout.com');
    }

    public function test_can_be_refunded_returns_true_when_amount_remaining_is_set()
    {
        $payment = new Payment(
            $this->createMock(MollieApiClient::class),
        );

        $amountRemaining = new Stdclass;
        $amountRemaining->value = '15.00';
        $amountRemaining->currency = 'EUR';

        $payment->amountRemaining = $amountRemaining;
        $this->assertTrue($payment->canBeRefunded());
        $this->assertTrue($payment->canBePartiallyRefunded());
    }

    public function test_can_be_refunded_returns_false_when_amount_remaining_is_null()
    {
        $payment = new Payment(
            $this->createMock(MollieApiClient::class),
        );

        $payment->amountRemaining = null;
        $this->assertFalse($payment->canBeRefunded());
        $this->assertFalse($payment->canBePartiallyRefunded());
    }

    public function test_get_amount_refunded_returns_amount_refunded_as_float()
    {
        $payment = new Payment(
            $this->createMock(MollieApiClient::class),
        );

        $payment->amountRefunded = (object) ['value' => 22.0, 'currency' => 'EUR'];
        self::assertSame(22.0, $payment->getAmountRefunded());
    }

    public function test_get_amount_refunded_returns0_when_amount_refunded_is_set_to_null()
    {
        $payment = new Payment(
            $this->createMock(MollieApiClient::class),
        );

        $payment->amountRefunded = null;
        self::assertSame(0.0, $payment->getAmountRefunded());
    }

    public function test_get_amount_remaining_returns_amount_remaining_as_float()
    {
        $payment = new Payment(
            $this->createMock(MollieApiClient::class),
        );

        $payment->amountRemaining = (object) ['value' => 22.0, 'currency' => 'EUR'];
        self::assertSame(22.0, $payment->getAmountRemaining());
    }

    public function test_get_amount_remaining_returns0_when_amount_remaining_is_set_to_null()
    {
        $payment = new Payment(
            $this->createMock(MollieApiClient::class),
        );

        $payment->amountRefunded = null;
        self::assertSame(0.0, $payment->getAmountRemaining());
    }

    public function test_get_amount_charged_back_returns_amount_charged_back_as_float()
    {
        $payment = new Payment(
            $this->createMock(MollieApiClient::class),
        );

        $payment->amountChargedBack = (object) ['value' => 22.0, 'currency' => 'EUR'];
        self::assertSame(22.0, $payment->getAmountChargedBack());
    }

    public function test_get_amount_charged_back_returns0_when_amount_charged_back_is_set_to_null()
    {
        $payment = new Payment(
            $this->createMock(MollieApiClient::class),
        );

        $payment->amountChargedBack = null;
        self::assertSame(0.0, $payment->getAmountChargedBack());
    }

    public function test_get_settlement_amount_returns0_when_settlement_amount_is_set_to_null()
    {
        $payment = new Payment(
            $this->createMock(MollieApiClient::class),
        );

        $payment->settlementAmount = null;
        self::assertSame(0.0, $payment->getSettlementAmount());
    }

    public function test_get_settlement_amount_returns_settlement_amount_as_float()
    {
        $payment = new Payment(
            $this->createMock(MollieApiClient::class),
        );

        $payment->settlementAmount = (object) ['value' => 22.0, 'currency' => 'EUR'];
        self::assertSame(22.0, $payment->getSettlementAmount());
    }

    public function test_has_split_payments_returns_false_when_payment_has_no_split()
    {
        $payment = new Payment(
            $this->createMock(MollieApiClient::class),
        );

        $payment->_links = new stdClass;
        $this->assertFalse($payment->hasSplitPayments());
    }
}

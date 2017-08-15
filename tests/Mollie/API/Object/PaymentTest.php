<?php

class Mollie_API_Object_PaymentTest extends PHPUnit_Framework_TestCase
{
	public function testIsCancelledReturnsTrueWhenStatusIsCancelled ()
	{
		$payment = new Mollie_API_Object_Payment();

		$payment->status = Mollie_API_Object_Payment::STATUS_CANCELLED;
		$this->assertTrue($payment->isCancelled());
	}

	public function testIsCancelledReturnsFalseWhenStatusIsNotCancelled ()
	{
		$payment = new Mollie_API_Object_Payment();

		$payment->status = NULL;
		$this->assertFalse($payment->isCancelled());

		$payment->status = Mollie_API_Object_Payment::STATUS_FAILED;
		$this->assertFalse($payment->isCancelled());
	}
	
	public function testIsExpiredReturnsTrueWhenStatusIsExpired ()
	{
		$payment = new Mollie_API_Object_Payment();

		$payment->status = Mollie_API_Object_Payment::STATUS_EXPIRED;
		$this->assertTrue($payment->isExpired());
	}

	public function testIsExpiredReturnsFalseWhenStatusIsNotExpired ()
	{
		$payment = new Mollie_API_Object_Payment();

		$payment->status = NULL;
		$this->assertFalse($payment->isExpired());

		$payment->status = Mollie_API_Object_Payment::STATUS_FAILED;
		$this->assertFalse($payment->isExpired());
	}

	public function testIsOpenReturnsTrueWhenStatusIsOpen ()
	{
		$payment = new Mollie_API_Object_Payment();

		$payment->status = Mollie_API_Object_Payment::STATUS_OPEN;
		$this->assertTrue($payment->isOpen());
	}

	public function testIsOpenReturnsFalseWhenStatusIsNotOpen ()
	{
		$payment = new Mollie_API_Object_Payment();

		$payment->status = NULL;
		$this->assertFalse($payment->isOpen());

		$payment->status = Mollie_API_Object_Payment::STATUS_FAILED;
		$this->assertFalse($payment->isOpen());
	}

	public function testIsPendingReturnsTrueWhenStatusIsPending ()
	{
		$payment = new Mollie_API_Object_Payment();

		$payment->status = Mollie_API_Object_Payment::STATUS_PENDING;
		$this->assertTrue($payment->isPending());
	}

	public function testIsPendingReturnsFalseWhenStatusIsNotPending ()
	{
		$payment = new Mollie_API_Object_Payment();

		$payment->status = NULL;
		$this->assertFalse($payment->isPending());

		$payment->status = Mollie_API_Object_Payment::STATUS_FAILED;
		$this->assertFalse($payment->isPending());
	}

	public function testIsPaidReturnsTrueWhenPaidDatetimeIsSet ()
	{
		$payment = new Mollie_API_Object_Payment();

		$payment->paidDatetime = "2016-10-24";
		$this->assertTrue($payment->isPaid());
	}

	public function testIsPaidReturnsFalseWhenStatusIsPaid ()
	{
		$payment = new Mollie_API_Object_Payment();

		$payment->status = Mollie_API_Object_Payment::STATUS_PAID;
		$this->assertFalse($payment->isPaid());
	}

	public function testIsPaidReturnsFalseWhenStatusIsNotPaid ()
	{
		$payment = new Mollie_API_Object_Payment();

		$payment->status = NULL;
		$this->assertFalse($payment->isPaid());

		$payment->status = Mollie_API_Object_Payment::STATUS_FAILED;
		$this->assertFalse($payment->isPaid());
	}

	public function testIsPaidOutReturnsTrueWhenStatusIsPaidOut ()
	{
		$payment = new Mollie_API_Object_Payment();

		$payment->status = Mollie_API_Object_Payment::STATUS_PAIDOUT;
		$this->assertTrue($payment->isPaidOut());
	}

	public function testIsPaidOutReturnsFalseWhenStatusIsNotPaidOut ()
	{
		$payment = new Mollie_API_Object_Payment();

		$payment->status = NULL;
		$this->assertFalse($payment->isPaidOut());

		$payment->status = Mollie_API_Object_Payment::STATUS_REFUNDED;
		$this->assertFalse($payment->isPaidOut());
	}

	public function testIsRefundedReturnsTrueWhenStatusIsRefunded ()
	{
		$payment = new Mollie_API_Object_Payment();

		$payment->status = Mollie_API_Object_Payment::STATUS_REFUNDED;
		$this->assertTrue($payment->isRefunded());
	}

	public function testIsRefundedReturnsFalseWhenStatusIsNotRefunded ()
	{
		$payment = new Mollie_API_Object_Payment();

		$payment->status = NULL;
		$this->assertFalse($payment->isRefunded());

		$payment->status = Mollie_API_Object_Payment::STATUS_CANCELLED;
		$this->assertFalse($payment->isRefunded());
	}

	public function testIsChargedBackReturnsTrueWhenStatusIsChargedBack ()
	{
		$payment = new Mollie_API_Object_Payment();

		$payment->status = Mollie_API_Object_Payment::STATUS_CHARGED_BACK;
		$this->assertTrue($payment->isChargedBack());
	}

	public function testIsChargedBackReturnsFalseWhenStatusIsNotChargedBack ()
	{
		$payment = new Mollie_API_Object_Payment();

		$payment->status = NULL;
		$this->assertFalse($payment->isChargedBack());

		$payment->status = Mollie_API_Object_Payment::STATUS_CANCELLED;
		$this->assertFalse($payment->isChargedBack());
	}

	public function testIsFailedReturnsTrueWhenStatusIsFailed ()
	{
		$payment = new Mollie_API_Object_Payment();

		$payment->status = Mollie_API_Object_Payment::STATUS_FAILED;
		$this->assertTrue($payment->isFailed());
	}

	public function testIsFailedReturnsFalseWhenStatusIsNotFailed ()
	{
		$payment = new Mollie_API_Object_Payment();

		$payment->status = NULL;
		$this->assertFalse($payment->isFailed());

		$payment->status = Mollie_API_Object_Payment::STATUS_OPEN;
		$this->assertFalse($payment->isFailed());
	}

	public function testHasRecurringTypeReturnsTrueWhenRecurringTypeIsFirst ()
	{
		$payment = new Mollie_API_Object_Payment();

		$payment->recurringType = Mollie_API_Object_Payment::RECURRINGTYPE_FIRST;
		$this->assertTrue($payment->hasRecurringType());
		$this->assertFalse($payment->hasRecurringTypeRecurring());
		$this->assertTrue($payment->hasRecurringTypeFirst());
	}

	public function testHasRecurringTypeReturnsTrueWhenRecurringTypeIsRecurring ()
	{
		$payment = new Mollie_API_Object_Payment();

		$payment->recurringType = Mollie_API_Object_Payment::RECURRINGTYPE_RECURRING;
		$this->assertTrue($payment->hasRecurringType());
		$this->assertTrue($payment->hasRecurringTypeRecurring());
		$this->assertFalse($payment->hasRecurringTypeFirst());
	}

	public function testHasRecurringTypeReturnsFalseWhenRecurringTypeIsNone ()
	{
		$payment = new Mollie_API_Object_Payment();

		$payment->recurringType = Mollie_API_Object_Payment::RECURRINGTYPE_NONE;
		$this->assertFalse($payment->hasRecurringType());
		$this->assertFalse($payment->hasRecurringTypeFirst());
		$this->assertFalse($payment->hasRecurringTypeRecurring());
	}

	public function testGetPaymentUrlReturnsPaymentUrlFromLinksObject ()
	{
		$payment = new Mollie_API_Object_Payment();

		$payment->links = new stdClass();
		$payment->links->paymentUrl = "https://example.com";

		$this->assertSame($payment->getPaymentUrl(), "https://example.com");
	}

	public function testCanBeRefundedReturnsTrueWhenAmountRemainingIsSet ()
	{
		$payment = new Mollie_API_Object_Payment();

		$payment->amountRemaining = 15;
		$this->assertTrue($payment->canBeRefunded());
		$this->assertTrue($payment->canBePartiallyRefunded());
	}

	public function testCanBeRefundedReturnsFalseWhenAmountRemainingIsNull ()
	{
		$payment = new Mollie_API_Object_Payment();

		$payment->amountRemaining = NULL;
		$this->assertFalse($payment->canBeRefunded());
		$this->assertFalse($payment->canBePartiallyRefunded());
	}

	public function testGetAmountRefundedReturnsAmountRefundedAsFloat ()
	{
		$payment = new Mollie_API_Object_Payment();

		$payment->amountRefunded = 22;
		self::assertSame(22.0, $payment->getAmountRefunded());
	}

	public function testGetAmountRefundedReturns0WhenAmountRefundedIsSetToNull ()
	{
		$payment = new Mollie_API_Object_Payment();

		$payment->amountRefunded = NULL;
		self::assertSame(0.0, $payment->getAmountRefunded());
	}

	public function testGetAmountRemainingReturnsAmountRemainingAsFloat ()
	{
		$payment = new Mollie_API_Object_Payment();

		$payment->amountRemaining = 22;
		self::assertSame(22.0, $payment->getAmountRemaining());
	}

	public function testGetAmountRemainingReturns0WhenAmountRemainingIsSetToNull ()
	{
		$payment = new Mollie_API_Object_Payment();

		$payment->amountRefunded  = NULL;
		self::assertSame(0.0, $payment->getAmountRemaining());
	}
}

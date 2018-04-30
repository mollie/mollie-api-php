<?php

namespace Tests\Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Settlement;
use Mollie\Api\Types\SettlementStatus;

class SettlementTest extends \PHPUnit_Framework_TestCase
{
    public function testIsOpenReturnsTrueWhenStatusIsOpen()
    {
        $refund = new Settlement($this->createMock(MollieApiClient::class));

        $refund->status = SettlementStatus::STATUS_OPEN;
        $this->assertTrue($refund->isOpen());
    }

    public function testIsOpenReturnsFalseWhenStatusIsNotOpen()
    {
        $refund = new Settlement($this->createMock(MollieApiClient::class));

        $refund->status = SettlementStatus::STATUS_PENDING;
        $this->assertFalse($refund->isOpen());
    }

    public function testIsPendingReturnsTrueWhenStatusIsPending()
    {
        $refund = new Settlement($this->createMock(MollieApiClient::class));

        $refund->status = SettlementStatus::STATUS_PENDING;
        $this->assertTrue($refund->isPending());
    }

    public function testIsPendingReturnsFalseWhenStatusIsNotPending()
    {
        $refund = new Settlement($this->createMock(MollieApiClient::class));

        $refund->status = SettlementStatus::STATUS_OPEN;
        $this->assertFalse($refund->isPending());
    }

    public function testIsPaidoutReturnsTrueWhenStatusIsPaidout()
    {
        $refund = new Settlement($this->createMock(MollieApiClient::class));

        $refund->status = SettlementStatus::STATUS_PAIDOUT;
        $this->assertTrue($refund->isPaidout());
    }

    public function testIsPaidoutReturnsFalseWhenStatusIsNotPaidout()
    {
        $refund = new Settlement($this->createMock(MollieApiClient::class));

        $refund->status = SettlementStatus::STATUS_OPEN;
        $this->assertFalse($refund->isPaidout());
    }

    public function testIsFailedReturnsTrueWhenStatusIsFailed()
    {
        $refund = new Settlement($this->createMock(MollieApiClient::class));

        $refund->status = SettlementStatus::STATUS_FAILED;
        $this->assertTrue($refund->isFailed());
    }

    public function testIsFailedReturnsFalseWhenStatusIsNotFailed()
    {
        $refund = new Settlement($this->createMock(MollieApiClient::class));

        $refund->status = SettlementStatus::STATUS_OPEN;
        $this->assertFalse($refund->isFailed());
    }
}

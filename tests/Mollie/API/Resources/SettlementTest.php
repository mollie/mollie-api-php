<?php

namespace Tests\Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Settlement;
use Mollie\Api\Types\SettlementStatus;

class SettlementTest extends \PHPUnit\Framework\TestCase
{
    public function testIsOpenReturnsTrueWhenStatusIsOpen()
    {
        $settlement = new Settlement($this->createMock(MollieApiClient::class));

        $settlement->status = SettlementStatus::STATUS_OPEN;
        $this->assertTrue($settlement->isOpen());
    }

    public function testIsOpenReturnsFalseWhenStatusIsNotOpen()
    {
        $settlement = new Settlement($this->createMock(MollieApiClient::class));

        $settlement->status = SettlementStatus::STATUS_PENDING;
        $this->assertFalse($settlement->isOpen());
    }

    public function testIsPendingReturnsTrueWhenStatusIsPending()
    {
        $settlement = new Settlement($this->createMock(MollieApiClient::class));

        $settlement->status = SettlementStatus::STATUS_PENDING;
        $this->assertTrue($settlement->isPending());
    }

    public function testIsPendingReturnsFalseWhenStatusIsNotPending()
    {
        $settlement = new Settlement($this->createMock(MollieApiClient::class));

        $settlement->status = SettlementStatus::STATUS_OPEN;
        $this->assertFalse($settlement->isPending());
    }

    public function testIsPaidoutReturnsTrueWhenStatusIsPaidout()
    {
        $settlement = new Settlement($this->createMock(MollieApiClient::class));

        $settlement->status = SettlementStatus::STATUS_PAIDOUT;
        $this->assertTrue($settlement->isPaidout());
    }

    public function testIsPaidoutReturnsFalseWhenStatusIsNotPaidout()
    {
        $settlement = new Settlement($this->createMock(MollieApiClient::class));

        $settlement->status = SettlementStatus::STATUS_OPEN;
        $this->assertFalse($settlement->isPaidout());
    }

    public function testIsFailedReturnsTrueWhenStatusIsFailed()
    {
        $settlement = new Settlement($this->createMock(MollieApiClient::class));

        $settlement->status = SettlementStatus::STATUS_FAILED;
        $this->assertTrue($settlement->isFailed());
    }

    public function testIsFailedReturnsFalseWhenStatusIsNotFailed()
    {
        $settlement = new Settlement($this->createMock(MollieApiClient::class));

        $settlement->status = SettlementStatus::STATUS_OPEN;
        $this->assertFalse($settlement->isFailed());
    }
}

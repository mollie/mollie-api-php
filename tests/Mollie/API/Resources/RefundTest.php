<?php

namespace Tests\Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Types\RefundStatus;

class RefundTest extends \PHPUnit\Framework\TestCase
{
    public function testIsQueuedReturnsTrueWhenStatusIsQueued()
    {
        $refund = new Refund($this->createMock(MollieApiClient::class));

        $refund->status = RefundStatus::STATUS_QUEUED;
        $this->assertTrue($refund->isQueued());
    }

    public function testIsPendingReturnsTrueWhenStatusIsPending()
    {
        $refund = new Refund($this->createMock(MollieApiClient::class));

        $refund->status = RefundStatus::STATUS_PENDING;
        $this->assertTrue($refund->isPending());
    }

    public function testIsProcessingReturnsTrueWhenStatusIsProcessing()
    {
        $refund = new Refund($this->createMock(MollieApiClient::class));

        $refund->status = RefundStatus::STATUS_PROCESSING;
        $this->assertTrue($refund->isProcessing());
    }

    public function testIsTransferredReturnsTrueWhenStatusIsRefunded()
    {
        $refund = new Refund($this->createMock(MollieApiClient::class));

        $refund->status = RefundStatus::STATUS_REFUNDED;
        $this->assertTrue($refund->isTransferred());
    }
}

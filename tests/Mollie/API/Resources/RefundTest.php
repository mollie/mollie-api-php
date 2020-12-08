<?php

namespace Tests\Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Types\RefundStatus;

class RefundTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param string $status
     * @param string $function
     * @param bool $expected_boolean
     *
     * @dataProvider dpTestRefundStatuses
     */
    public function testRefundStatuses($status, $function, $expected_boolean)
    {
        $refund = new Refund($this->createMock(MollieApiClient::class));
        $refund->status = $status;

        $this->assertEquals($expected_boolean, $refund->{$function}());
    }

    public function dpTestRefundStatuses()
    {
        return [
            [RefundStatus::STATUS_PENDING, "isPending", true],
            [RefundStatus::STATUS_PENDING, "isProcessing", false],
            [RefundStatus::STATUS_PENDING, "isQueued", false],
            [RefundStatus::STATUS_PENDING, "isTransferred", false],
            [RefundStatus::STATUS_PENDING, "isFailed", false],

            [RefundStatus::STATUS_PROCESSING, "isPending", false],
            [RefundStatus::STATUS_PROCESSING, "isProcessing", true],
            [RefundStatus::STATUS_PROCESSING, "isQueued", false],
            [RefundStatus::STATUS_PROCESSING, "isTransferred", false],
            [RefundStatus::STATUS_PROCESSING, "isFailed", false],

            [RefundStatus::STATUS_QUEUED, "isPending", false],
            [RefundStatus::STATUS_QUEUED, "isProcessing", false],
            [RefundStatus::STATUS_QUEUED, "isQueued", true],
            [RefundStatus::STATUS_QUEUED, "isTransferred", false],
            [RefundStatus::STATUS_QUEUED, "isFailed", false],

            [RefundStatus::STATUS_REFUNDED, "isPending", false],
            [RefundStatus::STATUS_REFUNDED, "isProcessing", false],
            [RefundStatus::STATUS_REFUNDED, "isQueued", false],
            [RefundStatus::STATUS_REFUNDED, "isTransferred", true],
            [RefundStatus::STATUS_REFUNDED, "isFailed", false],

            [RefundStatus::STATUS_FAILED, "isPending", false],
            [RefundStatus::STATUS_FAILED, "isProcessing", false],
            [RefundStatus::STATUS_FAILED, "isQueued", false],
            [RefundStatus::STATUS_FAILED, "isTransferred", false],
            [RefundStatus::STATUS_FAILED, "isFailed", true],
        ];
    }
}

<?php

declare(strict_types=1);

namespace Tests\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Refund;
use Mollie\Api\Types\RefundStatus;

class RefundTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param  string  $status
     * @param  string  $function
     * @param  bool  $expected_boolean
     *
     * @dataProvider dpTestRefundStatuses
     */
    public function test_refund_statuses($status, $function, $expected_boolean)
    {
        $refund = new Refund(
            $this->createMock(MollieApiClient::class),
        );
        $refund->status = $status;

        $this->assertEquals($expected_boolean, $refund->{$function}());
    }

    /**
     * @param  string  $status
     * @param  bool  $expected_boolean
     *
     * @dataProvider dpTestRefundCanBeCanceled
     */
    public function test_refund_can_be_canceled($status, $expected_boolean)
    {
        $refund = new Refund(
            $this->createMock(MollieApiClient::class),
        );
        $refund->status = $status;

        $this->assertEquals($expected_boolean, $refund->canBeCanceled());
    }

    public function dpTestRefundStatuses()
    {
        return [
            [RefundStatus::Pending->value, 'isPending', true],
            [RefundStatus::Pending->value, 'isProcessing', false],
            [RefundStatus::Pending->value, 'isQueued', false],
            [RefundStatus::Pending->value, 'isTransferred', false],
            [RefundStatus::Pending->value, 'isFailed', false],

            [RefundStatus::Processing->value, 'isPending', false],
            [RefundStatus::Processing->value, 'isProcessing', true],
            [RefundStatus::Processing->value, 'isQueued', false],
            [RefundStatus::Processing->value, 'isTransferred', false],
            [RefundStatus::Processing->value, 'isFailed', false],

            [RefundStatus::Queued->value, 'isPending', false],
            [RefundStatus::Queued->value, 'isProcessing', false],
            [RefundStatus::Queued->value, 'isQueued', true],
            [RefundStatus::Queued->value, 'isTransferred', false],
            [RefundStatus::Queued->value, 'isFailed', false],

            [RefundStatus::Refunded->value, 'isPending', false],
            [RefundStatus::Refunded->value, 'isProcessing', false],
            [RefundStatus::Refunded->value, 'isQueued', false],
            [RefundStatus::Refunded->value, 'isTransferred', true],
            [RefundStatus::Refunded->value, 'isFailed', false],

            [RefundStatus::Failed->value, 'isPending', false],
            [RefundStatus::Failed->value, 'isProcessing', false],
            [RefundStatus::Failed->value, 'isQueued', false],
            [RefundStatus::Failed->value, 'isTransferred', false],
            [RefundStatus::Failed->value, 'isFailed', true],
        ];
    }

    public function dpTestRefundCanBeCanceled()
    {
        return [
            [RefundStatus::Pending->value, true],
            [RefundStatus::Processing->value, false],
            [RefundStatus::Queued->value, true],
            [RefundStatus::Refunded->value, false],
            [RefundStatus::Failed->value, false],
            [RefundStatus::Canceled->value, false],
        ];
    }
}

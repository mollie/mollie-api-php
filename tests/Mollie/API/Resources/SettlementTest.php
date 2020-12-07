<?php

namespace Tests\Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Settlement;
use Mollie\Api\Types\SettlementStatus;

class SettlementTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param string $status
     * @param string $function
     * @param bool $expected_boolean
     *
     * @dataProvider dpTestSettlementStatuses
     */
    public function testSettlementStatuses($status, $function, $expected_boolean)
    {
        $settlement = new Settlement($this->createMock(MollieApiClient::class));
        $settlement->status = $status;

        $this->assertEquals($expected_boolean, $settlement->{$function}());
    }

    public function dpTestSettlementStatuses()
    {
        return [
            [SettlementStatus::STATUS_PENDING, "isPending", true],
            [SettlementStatus::STATUS_PENDING, "isOpen", false],
            [SettlementStatus::STATUS_PENDING, "isPaidout", false],
            [SettlementStatus::STATUS_PENDING, "isFailed", false],

            [SettlementStatus::STATUS_OPEN, "isPending", false],
            [SettlementStatus::STATUS_OPEN, "isOpen", true],
            [SettlementStatus::STATUS_OPEN, "isPaidout", false],
            [SettlementStatus::STATUS_OPEN, "isFailed", false],

            [SettlementStatus::STATUS_PAIDOUT, "isPending", false],
            [SettlementStatus::STATUS_PAIDOUT, "isOpen", false],
            [SettlementStatus::STATUS_PAIDOUT, "isPaidout", true],
            [SettlementStatus::STATUS_PAIDOUT, "isFailed", false],

            [SettlementStatus::STATUS_FAILED, "isPending", false],
            [SettlementStatus::STATUS_FAILED, "isOpen", false],
            [SettlementStatus::STATUS_FAILED, "isPaidout", false],
            [SettlementStatus::STATUS_FAILED, "isFailed", true],
        ];
    }
}

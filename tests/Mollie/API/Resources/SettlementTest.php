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
            [SettlementStatus::PENDING, "isPending", true],
            [SettlementStatus::PENDING, "isOpen", false],
            [SettlementStatus::PENDING, "isPaidout", false],
            [SettlementStatus::PENDING, "isFailed", false],

            [SettlementStatus::OPEN, "isPending", false],
            [SettlementStatus::OPEN, "isOpen", true],
            [SettlementStatus::OPEN, "isPaidout", false],
            [SettlementStatus::OPEN, "isFailed", false],

            [SettlementStatus::PAIDOUT, "isPending", false],
            [SettlementStatus::PAIDOUT, "isOpen", false],
            [SettlementStatus::PAIDOUT, "isPaidout", true],
            [SettlementStatus::PAIDOUT, "isFailed", false],

            [SettlementStatus::FAILED, "isPending", false],
            [SettlementStatus::FAILED, "isOpen", false],
            [SettlementStatus::FAILED, "isPaidout", false],
            [SettlementStatus::FAILED, "isFailed", true],
        ];
    }
}

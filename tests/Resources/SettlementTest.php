<?php

declare(strict_types=1);

namespace Tests\Resources;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Settlement;
use Mollie\Api\Types\SettlementStatus;

class SettlementTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param  string  $status
     * @param  string  $function
     * @param  bool  $expected_boolean
     *
     */
    #[DataProvider('dpTestSettlementStatuses')]
    #[Test]
    public function settlement_statuses($status, $function, $expected_boolean)
    {
        $settlement = new Settlement(
            $this->createMock(MollieApiClient::class),
        );
        $settlement->status = $status;

        $this->assertEquals($expected_boolean, $settlement->{$function}());
    }

    public static function dpTestSettlementStatuses()
    {
        return [
            [SettlementStatus::Pending->value, 'isPending', true],
            [SettlementStatus::Pending->value, 'isOpen', false],
            [SettlementStatus::Pending->value, 'isPaidout', false],
            [SettlementStatus::Pending->value, 'isFailed', false],

            [SettlementStatus::Open->value, 'isPending', false],
            [SettlementStatus::Open->value, 'isOpen', true],
            [SettlementStatus::Open->value, 'isPaidout', false],
            [SettlementStatus::Open->value, 'isFailed', false],

            [SettlementStatus::Paidout->value, 'isPending', false],
            [SettlementStatus::Paidout->value, 'isOpen', false],
            [SettlementStatus::Paidout->value, 'isPaidout', true],
            [SettlementStatus::Paidout->value, 'isFailed', false],

            [SettlementStatus::Failed->value, 'isPending', false],
            [SettlementStatus::Failed->value, 'isOpen', false],
            [SettlementStatus::Failed->value, 'isPaidout', false],
            [SettlementStatus::Failed->value, 'isFailed', true],
        ];
    }
}
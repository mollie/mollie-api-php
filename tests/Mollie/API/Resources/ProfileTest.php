<?php

namespace Tests\Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Profile;
use Mollie\Api\Types\ProfileStatus;

class ProfileTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param string $status
     * @param string $function
     * @param bool $expected_boolean
     *
     * @dataProvider dpTestProfileStatusses
     */
    public function testProfileStatusses($status, $function, $expected_boolean)
    {
        $profile = new Profile($this->createMock(MollieApiClient::class));
        $profile->status = $status;

        $this->assertEquals($expected_boolean, $profile->{$function}());
    }

    public function dpTestProfileStatusses()
    {
        return [
            [ProfileStatus::BLOCKED, "isBlocked", true],
            [ProfileStatus::BLOCKED, "isVerified", false],
            [ProfileStatus::BLOCKED, "isUnverified", false],

            [ProfileStatus::VERIFIED, "isBlocked", false],
            [ProfileStatus::VERIFIED, "isVerified", true],
            [ProfileStatus::VERIFIED, "isUnverified", false],

            [ProfileStatus::UNVERIFIED, "isBlocked", false],
            [ProfileStatus::UNVERIFIED, "isVerified", false],
            [ProfileStatus::UNVERIFIED, "isUnverified", true],
        ];
    }
}

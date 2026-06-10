<?php

declare(strict_types=1);

namespace Tests\Resources;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Profile;
use Mollie\Api\Types\ProfileStatus;

class ProfileTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @param  string  $status
     * @param  string  $function
     * @param  bool  $expected_boolean
     *
     */
    #[DataProvider('dpTestProfileStatusses')]
    #[Test]
    public function profile_statusses($status, $function, $expected_boolean)
    {
        $profile = new Profile(
            $this->createMock(MollieApiClient::class),
        );
        $profile->status = $status;

        $this->assertEquals($expected_boolean, $profile->{$function}());
    }

    public static function dpTestProfileStatusses()
    {
        return [
            [ProfileStatus::Blocked->value, 'isBlocked', true],
            [ProfileStatus::Blocked->value, 'isVerified', false],
            [ProfileStatus::Blocked->value, 'isUnverified', false],

            [ProfileStatus::Verified->value, 'isBlocked', false],
            [ProfileStatus::Verified->value, 'isVerified', true],
            [ProfileStatus::Verified->value, 'isUnverified', false],

            [ProfileStatus::Unverified->value, 'isBlocked', false],
            [ProfileStatus::Unverified->value, 'isVerified', false],
            [ProfileStatus::Unverified->value, 'isUnverified', true],
        ];
    }
}
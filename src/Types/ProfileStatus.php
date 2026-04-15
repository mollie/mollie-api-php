<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

enum ProfileStatus: string
{
    /**
     * The profile has not been verified yet and can only be used to create test payments.
     */
    case Unverified = 'unverified';

    /**
     * The profile has been verified and can be used to create live payments and test payments.
     */
    case Verified = 'verified';

    /**
     * The profile is blocked and can thus no longer be used or changed.
     */
    case Blocked = 'blocked';
}

<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

enum ApprovalPrompt: string
{
    case Auto = 'auto';

    /**
     * Force showing the consent screen to the merchant, even when it is not necessary.
     * Note that already active authorizations will be revoked when the user creates the new authorization.
     */
    case Force = 'force';
}

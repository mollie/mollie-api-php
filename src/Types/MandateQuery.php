<?php

namespace Mollie\Api\Types;

/**
 * @link https://docs.mollie.com/reference/list-mandates
 */
class MandateQuery
{
    const SCOPE_CUSTOMER_PRESENT = 'customer-present';

    const SCOPE_CUSTOMER_NOT_PRESENT = 'customer-not-present';

    const SCOPES = [
        self::SCOPE_CUSTOMER_PRESENT,
        self::SCOPE_CUSTOMER_NOT_PRESENT,
    ];
}

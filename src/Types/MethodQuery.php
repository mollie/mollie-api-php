<?php

namespace Mollie\Api\Types;

class MethodQuery
{
    const INCLUDE_PRICING = 'pricing';

    const INCLUDE_ISSUERS = 'issuers';

    const INCLUDES = [
        self::INCLUDE_PRICING,
        self::INCLUDE_ISSUERS,
    ];

    const SEQUENCE_TYPE_ONEOFF = 'oneoff';

    const SEQUENCE_TYPE_FIRST = 'first';

    const SEQUENCE_TYPE_RECURRING = 'recurring';

    const SEQUENCE_TYPES = [
        self::SEQUENCE_TYPE_ONEOFF,
        self::SEQUENCE_TYPE_FIRST,
        self::SEQUENCE_TYPE_RECURRING,
    ];

    const RESOURCE_ORDERS = 'orders';

    const RESOURCE_PAYMENTS = 'payments';

    const RESOURCES = [
        self::RESOURCE_ORDERS,
        self::RESOURCE_PAYMENTS,
    ];

    const WALLET_APPLEPAY = 'applepay';

    const WALLETS = [
        self::WALLET_APPLEPAY,
    ];
}

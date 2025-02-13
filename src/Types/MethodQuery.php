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

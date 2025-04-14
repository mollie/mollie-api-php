<?php

namespace Mollie\Api\Types;

class PaymentQuery
{
    const INCLUDE_QR_CODE = 'details.qrCode';

    const INCLUDE_REMAINDER_DETAILS = 'details.remainderDetails';

    const INCLUDES = [
        self::INCLUDE_QR_CODE,
        self::INCLUDE_REMAINDER_DETAILS,
    ];

    const EMBED_CAPTURES = 'captures';

    const EMBED_REFUNDS = 'refunds';

    const EMBED_CHARGEBACKS = 'chargebacks';

    const EMBEDS = [
        self::EMBED_CAPTURES,
        self::EMBED_REFUNDS,
        self::EMBED_CHARGEBACKS,
    ];
}

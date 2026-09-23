<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

enum OrderLineType: string
{
    case Physical = 'physical';
    case Discount = 'discount';
    case Digital = 'digital';
    case ShippingFee = 'shipping_fee';
    case StoreCredit = 'store_credit';
    case GiftCard = 'gift_card';
    case Surcharge = 'surcharge';
}

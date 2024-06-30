<?php

namespace Mollie\Api\Types;

class OrderLineType
{
    public const PHYSICAL = 'physical';
    public const DISCOUNT = 'discount';
    public const DIGITAL = 'digital';
    public const SHIPPING_FEE = 'shipping_fee';
    public const STORE_CREDIT = 'store_credit';
    public const GIFT_CARD = 'gift_card';
    public const SURCHARGE = 'surcharge';
}

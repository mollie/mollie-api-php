<?php

namespace Mollie\Api\Types;

class OrderLineCategory
{
    const CATEGORY_MEAL = 'meal';

    const CATEGORY_ECO = 'eco';

    const CATEGORY_GIFT = 'gift';

    const CATEGORY_SPORTS = 'sports';

    const CATEGORY_ADDITIONAL = 'additional';

    const CATEGORY_CONSUME = 'consume';

    const CATEGORIES = [
        self::CATEGORY_MEAL,
        self::CATEGORY_ECO,
        self::CATEGORY_GIFT,
        self::CATEGORY_SPORTS,
        self::CATEGORY_ADDITIONAL,
        self::CATEGORY_CONSUME,
    ];
}

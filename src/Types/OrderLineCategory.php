<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

enum OrderLineCategory: string
{
    case Meal = 'meal';
    case Eco = 'eco';
    case Gift = 'gift';
    case Sports = 'sports';
    case Additional = 'additional';
    case Consume = 'consume';
}

<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

enum BalanceTransferFrequency: string
{
    case Daily = 'daily';
    case TwiceAWeek = 'twice-a-week';
    case EveryMonday = 'every-monday';
    case EveryTuesday = 'every-tuesday';
    case EveryWednesday = 'every-wednesday';
    case EveryThursday = 'every-thursday';
    case EveryFriday = 'every-friday';
    case EverySaturday = 'every-saturday';
    case EverySunday = 'every-sunday';
    case TwiceAMonth = 'twice-a-month';
    case Never = 'never';
}

<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

enum SubscriptionStatus: string
{
    case Active = 'active';
    case Pending = 'pending';
    case Canceled = 'canceled';
    case Suspended = 'suspended';
    case Completed = 'completed';

    /**
     * Returns all values of the enum (backwards compatibility for GetAllConstants::all()).
     *
     * @return list<string>
     */
    public static function all(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }
}

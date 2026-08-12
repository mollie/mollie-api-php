<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Http\Data\Money;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class MethodPrice extends BaseResource
{
    public string $description;

    public Money $fixed;

    public string $variable;
}

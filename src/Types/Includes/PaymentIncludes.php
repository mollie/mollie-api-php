<?php

declare(strict_types=1);

namespace Mollie\Api\Types\Includes;

/**
 * @method static self qrCode()
 * @method static self remainderDetails()
 */
final class PaymentIncludes extends QueryParameterSet
{
    protected static function options(): array
    {
        return [
            'qrCode' => PaymentInclude::QrCode,
            'remainderDetails' => PaymentInclude::RemainderDetails,
        ];
    }
}

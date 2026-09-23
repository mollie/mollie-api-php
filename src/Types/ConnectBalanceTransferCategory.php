<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

enum ConnectBalanceTransferCategory: string
{
    case InvoiceCollection = 'invoice_collection';
    case Purchase = 'purchase';
    case Chargeback = 'chargeback';
    case Refund = 'refund';
    case ServicePenalty = 'service_penalty';
    case DiscountCompensation = 'discount_compensation';
    case ManualCorrection = 'manual_correction';
    case OtherFee = 'other_fee';

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }
}

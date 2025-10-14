<?php

namespace Mollie\Api\Types;

use Mollie\Api\Traits\GetAllConstants;

class ConnectBalanceTransferCategory
{
    use GetAllConstants;

    /**
     * Collecting invoice payments
     */
    public const INVOICE_COLLECTION = 'invoice_collection';

    /**
     * Purchase-related transfers
     */
    public const PURCHASE = 'purchase';

    /**
     * Chargeback transfers
     */
    public const CHARGEBACK = 'chargeback';

    /**
     * Refund transfers
     */
    public const REFUND = 'refund';

    /**
     * Service penalty fees
     */
    public const SERVICE_PENALTY = 'service_penalty';

    /**
     * Discount compensations
     */
    public const DISCOUNT_COMPENSATION = 'discount_compensation';

    /**
     * Manual corrections
     */
    public const MANUAL_CORRECTION = 'manual_correction';

    /**
     * Other fees
     */
    public const OTHER_FEE = 'other_fee';
}

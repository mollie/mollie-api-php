<?php

namespace Mollie\Api\Types;

class SalesInvoiceStatus
{
    /**
     * The sales invoice is in draft status and has not been sent or paid.
     */
    const DRAFT = 'draft';

    /**
     * The sales invoice has been issued to the customer but has not been paid yet.
     */
    const ISSUED = 'issued';

    /**
     * The sales invoice has been fully paid.
     */
    const PAID = 'paid';
}

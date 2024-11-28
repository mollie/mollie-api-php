<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\GetPaymentQuery;
use Mollie\Api\Types\PaymentQuery;

class GetPaymentQueryFactory extends Factory
{
    public function create(): GetPaymentQuery
    {
        $embedCaptures = $this->includes('embed', PaymentQuery::EMBED_CAPTURES);
        $embedRefunds = $this->includes('embed', PaymentQuery::EMBED_REFUNDS);
        $embedChargebacks = $this->includes('embed', PaymentQuery::EMBED_CHARGEBACKS);
        $includeQrCode = $this->includes('include', PaymentQuery::INCLUDE_QR_CODE);
        $includeRemainderDetails = $this->includes('include', PaymentQuery::INCLUDE_REMAINDER_DETAILS);

        return new GetPaymentQuery(
            $this->get('embedCaptures', $embedCaptures),
            $this->get('embedRefunds', $embedRefunds),
            $this->get('embedChargebacks', $embedChargebacks),
            $this->get('includeQrCode', $includeQrCode),
            $this->get('includeRemainderDetails', $includeRemainderDetails),
        );
    }
}

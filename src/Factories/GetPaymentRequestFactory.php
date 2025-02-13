<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Requests\GetPaymentRequest;
use Mollie\Api\Types\PaymentQuery;

class GetPaymentRequestFactory extends RequestFactory
{
    private string $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function create(): GetPaymentRequest
    {
        $embedCaptures = $this->queryIncludes('embed', PaymentQuery::EMBED_CAPTURES);
        $embedRefunds = $this->queryIncludes('embed', PaymentQuery::EMBED_REFUNDS);
        $embedChargebacks = $this->queryIncludes('embed', PaymentQuery::EMBED_CHARGEBACKS);
        $includeQrCode = $this->queryIncludes('include', PaymentQuery::INCLUDE_QR_CODE);
        $includeRemainderDetails = $this->queryIncludes('include', PaymentQuery::INCLUDE_REMAINDER_DETAILS);

        return new GetPaymentRequest(
            $this->id,
            $this->query('embedCaptures', $embedCaptures),
            $this->query('embedRefunds', $embedRefunds),
            $this->query('embedChargebacks', $embedChargebacks),
            $this->query('includeQrCode', $includeQrCode),
            $this->query('includeRemainderDetails', $includeRemainderDetails),
        );
    }
}

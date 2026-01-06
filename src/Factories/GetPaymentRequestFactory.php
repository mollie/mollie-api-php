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
        // Legacy: historically this factory accepted `embedCaptures` directly; Mollie uses `embed=captures`.
        $embedCaptures = $this->queryIncludes('embed', PaymentQuery::EMBED_CAPTURES);

        // Legacy: historically this factory accepted `embedRefunds` directly; Mollie uses `embed=refunds`.
        $embedRefunds = $this->queryIncludes('embed', PaymentQuery::EMBED_REFUNDS);

        // Legacy: historically this factory accepted `embedChargebacks` directly; Mollie uses `embed=chargebacks`.
        $embedChargebacks = $this->queryIncludes('embed', PaymentQuery::EMBED_CHARGEBACKS);

        // Legacy: historically this factory accepted `includeQrCode` directly; Mollie uses `include=details.qrCode`.
        $includeQrCode = $this->queryIncludes('include', PaymentQuery::INCLUDE_QR_CODE);

        // Legacy: historically this factory accepted `includeRemainderDetails` directly; Mollie uses `include=details.remainderDetails`.
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

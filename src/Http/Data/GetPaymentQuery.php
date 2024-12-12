<?php

namespace Mollie\Api\Http\Data;

use Mollie\Api\Helpers\Arr;
use Mollie\Api\Types\PaymentQuery;

class GetPaymentQuery extends Data
{
    public bool $embedCaptures = false;

    public bool $embedRefunds = false;

    public bool $embedChargebacks = false;

    public bool $includeQrCode = false;

    public bool $includeRemainderDetails = false;

    public function __construct(
        bool $embedCaptures = false,
        bool $embedRefunds = false,
        bool $embedChargebacks = false,
        bool $includeQrCode = false,
        bool $includeRemainderDetails = false
    ) {
        $this->embedCaptures = $embedCaptures;
        $this->embedRefunds = $embedRefunds;
        $this->embedChargebacks = $embedChargebacks;
        $this->includeQrCode = $includeQrCode;
        $this->includeRemainderDetails = $includeRemainderDetails;
    }

    public function toArray(): array
    {
        return [
            'embed' => Arr::join([
                $this->embedCaptures ? PaymentQuery::EMBED_CAPTURES : null,
                $this->embedRefunds ? PaymentQuery::EMBED_REFUNDS : null,
                $this->embedChargebacks ? PaymentQuery::EMBED_CHARGEBACKS : null,
            ]),
            'include' => Arr::join([
                $this->includeQrCode ? PaymentQuery::INCLUDE_QR_CODE : null,
                $this->includeRemainderDetails ? PaymentQuery::INCLUDE_REMAINDER_DETAILS : null,
            ]),
        ];
    }
}

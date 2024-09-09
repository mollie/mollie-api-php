<?php

namespace Mollie\Api\Http\Query;

class GetPaginatedChargebackQuery extends PaginatedQuery
{
    public bool $includePayment = false;

    public ?string $profileId = null;

    public function __construct(
        bool $includePayment = false,
        ?string $profileId = null,
        ?string $from = null,
        ?int $limit = null,
        ?bool $testmode = null
    ) {
        parent::__construct($from, $limit, $testmode);

        $this->includePayment = $includePayment;
        $this->profileId = $profileId;
    }

    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            [
                'include' => $this->includePayment ? 'payment' : null,
                'profileId' => $this->profileId,
            ]
        );
    }
}

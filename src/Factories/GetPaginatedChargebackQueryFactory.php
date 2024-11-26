<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Query\GetPaginatedChargebackQuery;

class GetPaginatedChargebackQueryFactory extends Factory
{
    private PaginatedQueryFactory $paginatedQueryFactory;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->paginatedQueryFactory = new PaginatedQueryFactory($attributes);
    }

    public function create(): GetPaginatedChargebackQuery
    {
        return new GetPaginatedChargebackQuery(
            $this->paginatedQueryFactory->create(),
            $this->get('includePayment', false),
            $this->get('profileId')
        );
    }
}

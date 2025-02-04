<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Http\Data\DataCollection;
use Mollie\Api\Http\Data\RefundRoute;
use Mollie\Api\Utils\Arr;

class RefundRouteCollectionFactory extends Factory
{
    public function create(): DataCollection
    {
        $refundRoutes = array_map(function (array $item) {
            if (! $this->has(['amount', 'source.organizationId'])) {
                throw new \InvalidArgumentException('Invalid RefundRoute data provided');
            }

            return new RefundRoute(
                MoneyFactory::new(Arr::get($item, 'amount'))->create(),
                Arr::get($item, 'source.organizationId')
            );
        }, $this->get());

        return new DataCollection($refundRoutes);
    }
}

<?php

namespace Mollie\Api\Factories;

use Mollie\Api\Exceptions\LogicException;
use Mollie\Api\Http\Data\DataCollection;
use Mollie\Api\Http\Data\RefundRoute;
use Mollie\Api\Utils\Arr;

class RefundRouteCollectionFactory extends Factory
{
    public function create(): DataCollection
    {
        $refundRoutes = array_map(function ($item) {
            if ($item instanceof RefundRoute) {
                return $item;
            }

            if (! $this->has(['amount', 'source.organizationId'], $item)) {
                throw new LogicException('Invalid RefundRoute data provided');
            }

            return new RefundRoute(
                MoneyFactory::new(Arr::get($item, 'amount'))->create(),
                Arr::get($item, 'source.organizationId')
            );
        }, $this->get());

        return new DataCollection($refundRoutes);
    }
}

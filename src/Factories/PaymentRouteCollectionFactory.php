<?php

namespace Mollie\Api\Factories;

use DateTimeImmutable;
use Mollie\Api\Exceptions\LogicException;
use Mollie\Api\Http\Data\DataCollection;
use Mollie\Api\Http\Data\PaymentRoute;
use Mollie\Api\Utils\Arr;
use Mollie\Api\Utils\Utility;

class PaymentRouteCollectionFactory extends Factory
{
    public function create(): DataCollection
    {
        $paymentRoutes = array_map(function ($item) {
            if ($item instanceof PaymentRoute) {
                return $item;
            }

            if (! $this->has(['amount', 'destination.organizationId'], $item)) {
                throw new LogicException('Invalid PaymentRoute data provided');
            }

            return new PaymentRoute(
                MoneyFactory::new(Arr::get($item, 'amount'))->create(),
                Arr::get($item, 'destination.organizationId'),
                Utility::compose(
                    Arr::get($item, 'delayUntil'),
                    fn ($value) => DateTimeImmutable::createFromFormat('Y-m-d', $value),
                    DateTimeImmutable::class
                )
            );
        }, $this->get());

        return new DataCollection($paymentRoutes);
    }
}

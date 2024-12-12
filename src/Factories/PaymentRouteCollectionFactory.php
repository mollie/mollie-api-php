<?php

namespace Mollie\Api\Factories;

use DateTimeImmutable;
use Mollie\Api\Helpers;
use Mollie\Api\Helpers\Arr;
use Mollie\Api\Http\Data\DataCollection;
use Mollie\Api\Http\Data\PaymentRoute;

class PaymentRouteCollectionFactory extends Factory
{
    public function create(): DataCollection
    {
        $paymentRoutes = array_map(function (array $item) {
            if (! $this->has(['amount', 'destination.organizationId'])) {
                throw new \InvalidArgumentException('Invalid PaymentRoute data provided');
            }

            return new PaymentRoute(
                MoneyFactory::new(Arr::get($item, 'amount'))->create(),
                Arr::get($item, 'destination.organizationId'),
                Helpers::compose(
                    Arr::get($item, 'delayUntil'),
                    fn($value) => DateTimeImmutable::createFromFormat('Y-m-d', $value)
                )
            );
        }, $this->data);

        return new DataCollection($paymentRoutes);
    }
}

<?php

namespace Mollie\Api\Factories;

use DateTimeImmutable;
use Mollie\Api\Http\Data\DataCollection;
use Mollie\Api\Http\Data\PaymentRoute;
use Mollie\Api\Utils\Arr;
use Mollie\Api\Utils\Utility;

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
                Utility::compose(
                    Arr::get($item, 'delayUntil'),
                    fn ($value) => DateTimeImmutable::createFromFormat('Y-m-d', $value)
                )
            );
        }, $this->data);

        return new DataCollection($paymentRoutes);
    }
}

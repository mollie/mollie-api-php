<?php

namespace Mollie\Api\Utils;

use DateTimeInterface;
use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Contracts\HasPayload;
use Mollie\Api\Contracts\PayloadRepository;
use Mollie\Api\Contracts\Resolvable;
use Mollie\Api\Contracts\Stringable;
use Mollie\Api\Http\Data\DataCollection;
use Mollie\Api\Http\PendingRequest;

class DataTransformer
{
    public function transform(PendingRequest $pendingRequest): PendingRequest
    {
        if ($pendingRequest->query()->isNotEmpty()) {
            $transformedQuery = $this->resolveQuery($pendingRequest->query()->all());

            $pendingRequest->query()->set($transformedQuery);
        }

        if (! $pendingRequest->getRequest() instanceof HasPayload) {
            return $pendingRequest;
        }

        /** @var PayloadRepository $payload */
        $payload = $pendingRequest->payload();

        if ($payload->isNotEmpty()) {
            $transformedPayload = $this->resolve($payload->all());

            $payload->set($transformedPayload);
        }

        return $pendingRequest;
    }

    private function resolveQuery(array $query): array
    {
        return $this->resolve($query, function ($value) {
            return $this->transformBooleans($value);
        });
    }

    private function resolve(array $values, $mapResolver = null): array
    {
        return DataCollection::wrap($values)
            ->map(function ($value) use ($mapResolver) {
                $value = is_callable($mapResolver) ? $mapResolver($value) : $value;

                if ($value instanceof Resolvable) {
                    return $this->resolve($value->toArray());
                }

                if ($value instanceof Arrayable) {
                    return array_filter($value->toArray(), fn ($value) => $this->filterEmptyValues($value));
                }

                if ($value instanceof Stringable) {
                    return $value->__toString();
                }

                if ($value instanceof DateTimeInterface) {
                    return $value->format('Y-m-d');
                }

                return $value;
            })
            ->filter(fn ($value) => $this->filterEmptyValues($value))
            ->toArray();
    }

    private function filterEmptyValues($value)
    {
        return ! empty($value) || is_bool($value);
    }

    private function transformBooleans($value)
    {
        if (is_array($value)) {
            return Arr::map($value, function ($value) {
                return $this->transformBooleans($value);
            });
        }

        if ($value === true) {
            return 'true';
        }

        if ($value === false) {
            return 'false';
        }

        return $value;
    }
}

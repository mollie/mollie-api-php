<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Contracts\ArrayRepository;
use Mollie\Api\Http\Request;

trait ResolvesUri
{
    protected function resolveUri(Request $request): string
    {
        $path = $request->resolveResourcePath();

        $query = $this->buildQuery($request->query());

        return $path.$query;
    }

    private function buildQuery(ArrayRepository $query): string
    {
        if ($query->isEmpty()) {
            return '';
        }

        $query = $this->transformQuery($query->all());

        return '?'.http_build_query($query, '', '&');
    }

    private function transformQuery(array $query): array
    {
        return array_map(function ($value) {
            if ($value === true) {
                return 'true';
            }

            if ($value === false) {
                return 'false';
            }

            return $value;
        }, $query);
    }
}

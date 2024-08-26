<?php

namespace Mollie\Api\Http;

/**
 * @mixin Endpoint
 */
trait HandlesQueries
{
    protected function buildQueryString(array $filters): string
    {
        if (empty($filters)) {
            return '';
        }

        foreach ($filters as $key => $value) {
            if ($value === true) {
                $filters[$key] = 'true';
            }

            if ($value === false) {
                $filters[$key] = 'false';
            }
        }

        return '?'.http_build_query($filters, '', '&');
    }
}

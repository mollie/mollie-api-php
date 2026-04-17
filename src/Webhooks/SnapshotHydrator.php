<?php

namespace Mollie\Api\Webhooks;

use Mollie\Api\Resources\BaseResource;
use Mollie\Api\Resources\ResourceHydrator;

/**
 * Thin adapter that feeds a webhook snapshot array through the main
 * {@see ResourceHydrator}. The only transformation performed is a
 * `json_decode(json_encode(...))` round-trip which produces the same
 * nested-stdClass shape that `Response::json()` produces from an HTTP
 * body. That makes `$payment->amount->value` (object notation, used
 * throughout the docs and recipes) work identically across origins.
 */
final class SnapshotHydrator
{
    private ResourceHydrator $inner;

    public function __construct(?ResourceHydrator $inner = null)
    {
        $this->inner = $inner ?? new ResourceHydrator;
    }

    /**
     * @param  array<string, mixed>  $snapshot
     */
    public function hydrate(
        BaseResource $resource,
        array $snapshot,
        WebhookSnapshotOrigin $origin
    ): BaseResource {
        $tree = json_decode(json_encode($snapshot));

        return $this->inner->hydrate($resource, $tree, $origin);
    }
}

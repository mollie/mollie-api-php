<?php

namespace Mollie\Api\Contracts;

/**
 * Marker contract for "where did this hydrated resource come from."
 *
 * Implemented by {@see \Mollie\Api\Http\Response} for HTTP-hydrated
 * resources and by {@see \Mollie\Api\Webhooks\WebhookSnapshotOrigin}
 * for resources hydrated from a signed webhook envelope. Additional
 * implementations can be added without touching the hydrator.
 */
interface ResourceOrigin
{
    public function getConnector(): Connector;
}

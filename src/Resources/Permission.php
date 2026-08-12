<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

/**
 * OAuth permission returned by the Permissions API.
 *
 * @property \Mollie\Api\MollieApiClient $connector
 * @link https://docs.mollie.com/docs/permissions Full list of permission IDs (scopes)
 */
class Permission extends BaseResource
{
    /** @example payments.read */
    public string $id;

    public string $description;

    public bool $granted;

    /**
     * @var \stdClass
     */
    public $_links;
}

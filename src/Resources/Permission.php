<?php

namespace Mollie\Api\Resources;

/**
 * OAuth permission returned by the Permissions API.
 *
 * @property \Mollie\Api\MollieApiClient $connector
 * @link https://docs.mollie.com/docs/permissions Full list of permission IDs (scopes)
 */
class Permission extends BaseResource
{
    /**
     * Permission ID (scope), e.g. payments.read, onboarding.write.
     *
     * @var string
     * @example payments.read
     */
    public $id;

    /**
     * @var string
     */
    public $description;

    /**
     * @var bool
     */
    public $granted;

    /**
     * @var \stdClass
     */
    public $_links;
}

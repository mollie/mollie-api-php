<?php

namespace Mollie\Api\Resources;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class Permission extends BaseResource
{
    /**
     * @var string
     *
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

<?php

namespace Mollie\Api\Resources;

class Capability extends BaseResource
{
    /**
     * @var string
     */
    public $resource;

    /**
     * @var string
     */
    public $name;

    /**
     * @var \stdClass
     */
    public $requirements;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    public $statusReason;

    public $organizationId;

    /**
     * Links to help navigate through the Mollie API and related resources.
     *
     * @var \stdClass
     */
    public $_links;
}

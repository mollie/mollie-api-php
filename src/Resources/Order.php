<?php

namespace Mollie\Api\Resources;

class Order extends BaseResource
{
    /**
     * @var string
     */
    public $resource;

    /**
     * Id of the order.
     *
     * @var string
     */
    public $id;

    /**
     * Either "live" or "test". Indicates this being a test or a live (verified) order.
     *
     * @var string
     */
    public $mode;
}

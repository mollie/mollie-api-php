<?php

namespace Mollie\Api\Resources;

class Method
{
    /**
     * Id of the payment method.
     *
     * @var string
     */
    public $id;

    /**
     * More legible description of the payment method.
     *
     * @var string
     */
    public $description;

    /**
     * The $image->size1x and $image->size2x to display the payment method logo.
     *
     * @var object
     */
    public $image;

    /**
     * @var object[]
     */
    public $_links;
}

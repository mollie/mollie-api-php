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
     * The $amount->minimum and $amount->maximum supported by this method and the used API key.
     *
     * @var object
     */
    public $amount;

    /**
     * The $image->normal and $image->bigger to display the payment method logo.
     *
     * @var object
     */
    public $image;

    /**
     * @return float|null
     */
    public function getMinimumAmount()
    {
        if (empty($this->amount)) {
            return null;
        }

        return (float)$this->amount->minimum;
    }

    /**
     * @return float|null
     */
    public function getMaximumAmount()
    {
        if (empty($this->amount)) {
            return null;
        }

        return (float)$this->amount->maximum;
    }
}

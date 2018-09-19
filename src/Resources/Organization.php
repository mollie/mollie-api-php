<?php

namespace Mollie\Api\Resources;

class Organization extends BaseResource
{
    /**
     * Id of the payment method.
     *
     * @var string
     */
    public $id;

    /**
     * The name of the organization.
     *
     * @var string
     */
    public $name;

    /**
     * The address of the organization.
     *
     * @var object
     */
    public $address;

    /**
     * The registration number of the organization at the (local) chamber of
     * commerce.
     *
     * @var string
     */
    public $registrationNumber;

    /**
     * The VAT number of the organization, if based in the European Union. The VAT
     * number has been checked with the VIES by Mollie.
     *
     * @var string
     */
    public $vatNumber;

    /**
     * @var object[]
     */
    public $_links;
}

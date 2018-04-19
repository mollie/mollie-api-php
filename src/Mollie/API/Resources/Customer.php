<?php

namespace Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;

class Customer extends BaseResource
{
    /**
     * @var string
     */
    public $resource;

    /**
     * Id of the customer.
     *
     * @var string
     */
    public $id;

    /**
     * Either "live" or "test". Indicates this being a test or a live (verified) customer.
     *
     * @var string
     */
    public $mode;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string|null
     */
    public $locale;

    /**
     * @var object|mixed|null
     */
    public $metadata;

    /**
     * @var string[]|array
     */
    public $recentlyUsedMethods;

    /**
     * @var string
     */
    public $createdAt;

    /**
     * @var object[]
     */
    public $_links;

    /**
     * @return BaseResource
     */
    public function update()
    {
        if (!isset($this->_links->self->href)) {
            return $this;
        }

        $body = json_encode(array(
            "name" => $this->name,
            "email" => $this->email,
            "locale" => $this->locale,
            "metadata" => $this->metadata,
        ));

        $result = $this->client->performHttpCallToFullUrl(MollieApiClient::HTTP_POST, $this->_links->self->href, $body);

        return ResourceFactory::createFromApiResult($result, new Customer($this->client));
    }

}
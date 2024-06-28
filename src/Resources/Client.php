<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Contracts\EmbeddedResourcesContract;

class Client extends BaseResource implements EmbeddedResourcesContract
{
    /**
     * The unique identifier of the client, which corresponds to the ID of the organization
     *
     * @var string
     */
    public $id;

    /**
     * UTC datetime the order was created in ISO-8601 format.
     *
     * @example "2018-03-21T13:13:37+00:00"
     * @var string|null
     */
    public $organizationCreatedAt;

    /**
     * @var \stdClass
     */
    public $_links;

    /**
     * @var \stdClass[]
     */
    public $_embedded;

    /**
     * @var \stdClass|null
     */
    public $commission;

    public function getEmbeddedResourcesMap(): array
    {
        return [
            'organization' => Organization::class,
            'onboarding' => Onboarding::class,
        ];
    }
}

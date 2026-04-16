<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Contracts\EmbeddedResourcesContract;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class Client extends BaseResource implements EmbeddedResourcesContract
{
    public string $id;

    public ?string $organizationCreatedAt = null;

    /**
     * @var \stdClass
     */
    public $_links;

    /**
     * @var \stdClass
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

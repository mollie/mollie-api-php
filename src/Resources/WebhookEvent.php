<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class WebhookEvent extends BaseResource
{

    public string $id;

    public string $type;

    public string $entityId;

    public string $createdAt;

    /**
     * @var \stdClass|null
     */
    public $_embedded;

    /**
     * @var \stdClass
     */
    public $_links;

    public function getEntity(): ?\stdClass
    {
        return $this->_embedded->entity ?? null;
    }

    public function hasEntity(): bool
    {
        return ! empty($this->_embedded->entity);
    }
}

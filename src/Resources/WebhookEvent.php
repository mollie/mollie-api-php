<?php

namespace Mollie\Api\Resources;

/**
 * @property \Mollie\Api\MollieApiClient $connector
 */
class WebhookEvent extends BaseResource
{
    /**
     * Indicates the response contains a webhook event object.
     * Will always contain the string "event" for this endpoint.
     *
     * @var string
     */
    public $resource;

    /**
     * The identifier uniquely referring to this event.
     *
     * @var string
     */
    public $id;

    /**
     * The event's type.
     *
     * @var string
     */
    public $type;

    /**
     * The entity token that triggered the event.
     *
     * @var string
     */
    public $entityId;

    /**
     * The event's date time of creation in ISO-8601 format.
     *
     * @example "2023-12-25T10:30:54+00:00"
     *
     * @var string
     */
    public $createdAt;

    /**
     * Full payload of the event.
     * Contains an 'entity' key with the full object payload.
     *
     * @var \stdClass|null
     */
    public $_embedded;

    /**
     * An object with several relevant URLs.
     * Every URL object will contain an href and a type field.
     *
     * @var \stdClass
     */
    public $_links;

    /**
     * Get the embedded entity data from the webhook event.
     *
     * @return \stdClass|null
     */
    public function getEntity()
    {
        return $this->_embedded->entity ?? null;
    }

    /**
     * Check if this webhook event has embedded entity data.
     */
    public function hasEntity(): bool
    {
        return ! empty($this->_embedded->entity);
    }
}

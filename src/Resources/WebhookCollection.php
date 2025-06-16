<?php

namespace Mollie\Api\Resources;

class WebhookCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     */
    public static string $collectionName = 'webhooks';

    /**
     * Resource class name.
     */
    public static string $resource = Webhook::class;
}

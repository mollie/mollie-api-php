<?php

namespace Mollie\Api\Resources;

class CapabilityCollection extends ResourceCollection
{
    public static string $collectionName = 'capabilities';

    public static string $resource = Capability::class;
}

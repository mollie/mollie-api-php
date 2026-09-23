<?php

declare(strict_types=1);

namespace Mollie\Api\Contracts;

interface EmbeddedResourcesContract
{
    public function getEmbeddedResourcesMap(): array;
}

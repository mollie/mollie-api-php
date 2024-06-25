<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Contracts\DependableEndpoint;

/**
 * @mixin DependableEndpoint
 */
trait IsDependableOnParent
{
    protected ?string $parentId = null;

    public function getParentId(): ?string
    {
        return $this->parentId;
    }
}

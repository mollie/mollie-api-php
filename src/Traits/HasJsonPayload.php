<?php

namespace Mollie\Api\Traits;

use Mollie\Api\Repositories\JsonBodyRepository;

trait HasJsonPayload
{
    public ?JsonBodyRepository $body = null;

    public function payload(): JsonBodyRepository
    {
        return $this->body ??= new JsonBodyRepository($this->defaultPayload());
    }

    protected function defaultPayload(): array
    {
        return [];
    }
}

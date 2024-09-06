<?php

namespace Mollie\Api\Contracts;

interface Factory
{
    public static function new(array $data): static;

    public function create(): mixed;
}

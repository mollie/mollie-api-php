<?php

namespace Mollie\Api\Contracts;

interface ResponseContract
{
    public function body(): string;

    /**
     * @return \stdClass
     */
    public function decode(): \stdClass;

    public function status(): int;

    public function isEmpty(): bool;
}

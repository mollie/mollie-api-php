<?php

namespace Mollie\Api\Contracts;

interface ResponseContract
{
    public function body(): string;

    /**
     * @return \stdClass
     */
    public function json(): \stdClass;

    public function status(): int;
}

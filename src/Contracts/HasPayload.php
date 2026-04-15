<?php

declare(strict_types=1);

namespace Mollie\Api\Contracts;

interface HasPayload
{
    public function payload(): PayloadRepository;
}

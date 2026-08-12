<?php

declare(strict_types=1);

namespace Mollie\Api\Contracts;

interface IsIteratable
{
    public function iteratorEnabled(): bool;

    public function iteratesBackwards(): bool;
}

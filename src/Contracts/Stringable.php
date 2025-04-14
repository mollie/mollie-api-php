<?php

namespace Mollie\Api\Contracts;

/**
 * @internal
 *
 * Remove this once we drop support for PHP 7.4
 */
interface Stringable
{
    public function __toString(): string;
}

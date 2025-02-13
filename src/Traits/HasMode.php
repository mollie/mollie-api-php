<?php

namespace Mollie\Api\Traits;

/**
 * @property string $mode 'live' or 'test'
 */
trait HasMode
{
    public function isInTestmode(): bool
    {
        return $this->mode === 'test';
    }

    public function withMode(array $options = []): array
    {
        return array_merge($options, ['testmode' => $this->isInTestmode()]);
    }
}

<?php

namespace Mollie\Api\Resources;

/**
 * @mixin BaseResource
 */
trait HasPresetOptions
{
    /**
     * When accessed by oAuth we want to pass the testmode by default
     */
    protected function getPresetOptions(): array
    {
        $options = [];

        if ($this->client->usesOAuth()) {
            $options['testmode'] = $this->mode === 'test' ? true : false;
        }

        return $options;
    }

    /**
     * Apply the preset options.
     */
    protected function withPresetOptions(array $options): array
    {
        return array_merge($this->getPresetOptions(), $options);
    }
}

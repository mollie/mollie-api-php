<?php

namespace Mollie\Api\Resources;

/**
 * @mixin BaseResource
 */
trait HasPresetOptions
{
    /**
     * When accessed by oAuth we want to pass the testmode by default
     *
     * @return array
     */
    protected function getPresetOptions(): array
    {
        $options = [];

        if ($this->client->usesOAuth()) {
            $options["testmode"] = $this->mode === "test" ? true : false;
        }

        return $options;
    }

    /**
     * Apply the preset options.
     *
     * @param array $options
     * @return array
     */
    protected function withPresetOptions(array $options): array
    {
        return array_merge($this->getPresetOptions(), $options);
    }
}

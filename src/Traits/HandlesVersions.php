<?php

namespace Mollie\Api\Traits;

use Mollie\Api\MollieApiClient;

/**
 * @mixin MollieApiClient
 */
trait HandlesVersions
{
    protected array $versionStrings = [];

    protected function initializeHandlesVersions(): void
    {
        $this->addVersionString('Mollie/'.MollieApiClient::CLIENT_VERSION);
        $this->addVersionString('PHP/'.phpversion());

        if ($clientVersion = $this->httpClient->version()) {
            $this->addVersionString($clientVersion);
        }
    }

    /**
     * @param  string  $versionString
     */
    public function addVersionString($versionString): self
    {
        $this->versionStrings[] = str_replace([' ', "\t", "\n", "\r"], '-', $versionString);

        return $this;
    }

    public function getVersionStrings(): array
    {
        return $this->versionStrings;
    }
}

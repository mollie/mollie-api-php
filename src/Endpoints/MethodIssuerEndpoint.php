<?php

declare(strict_types=1);

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Issuer;

class MethodIssuerEndpoint extends RestEndpoint
{
    protected string $resourcePath = 'profiles_methods_issuers';

    protected $profileId = null;
    protected $methodId = null;
    protected $issuerId = null;

    public static function getResourceClass(): string
    {
        return Issuer::class;
    }

    /**
     * @param string $profileId
     * @param string $methodId
     * @param string $issuerId
     * @return Issuer
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function enable(string $profileId, string $methodId, string $issuerId)
    {
        $this->profileId = $profileId;
        $this->methodId = $methodId;
        $this->issuerId = $issuerId;

        $response = $this->createResource([], []);

        $this->resetResourceIds();

        return $response;
    }

    public function disable(string $profileId, string $methodId, string $issuerId)
    {
        $this->profileId = $profileId;
        $this->methodId = $methodId;

        return $this->deleteResource($issuerId);
    }

    protected function resetResourceIds()
    {
        $this->profileId = null;
        $this->methodId = null;
        $this->issuerId = null;
    }

    /**
     * @return string
     * @throws ApiException
     */
    public function getResourcePath(): string
    {
        if (!$this->profileId) {
            throw new ApiException("No profileId provided.");
        }

        if (!$this->methodId) {
            throw new ApiException("No methodId provided.");
        }

        $path = "profiles/{$this->profileId}/methods/{$this->methodId}/issuers";

        if ($this->issuerId) {
            $path .= "/$this->issuerId";
        }

        return $path;
    }
}

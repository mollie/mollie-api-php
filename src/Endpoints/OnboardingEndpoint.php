<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Contracts\SingleResourceEndpointContract;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Onboarding;

class OnboardingEndpoint extends RestEndpoint implements SingleResourceEndpointContract
{
    protected string $resourcePath = "onboarding/me";

    /**
     * @inheritDoc
     */
    public static function getResourceClass(): string
    {
        return  Onboarding::class;
    }

    /**
     * Retrieve the organization's onboarding status from Mollie.
     *
     * Will throw a ApiException if the resource cannot be found.
     *
     * @return Onboarding
     * @throws ApiException
     */
    public function get(): Onboarding
    {
        /** @var Onboarding */
        return $this->readResource('', []);
    }

    /**
     * Submit data that will be prefilled in the merchant’s onboarding.
     * Please note that the data you submit will only be processed when the onboarding status is needs-data.
     *
     * Information that the merchant has entered in their dashboard will not be overwritten.
     *
     * Will throw a ApiException if the resource cannot be found.
     *
     * @return void
     * @throws ApiException
     * @deprecated use ClientLinkEndpoint create() method
     */
    public function submit(array $parameters = []): void
    {
        $this->create($parameters, []);
    }

    /**
     * @param array $body
     * @param array $filters
     *
     * @return void
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    private function create(array $body, array $filters): void
    {
        $this->client->performHttpCall(
            self::REST_CREATE,
            $this->getResourcePath() . $this->buildQueryString($filters),
            $this->parseRequestBody($body)
        );
    }
}

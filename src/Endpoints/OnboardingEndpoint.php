<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Contracts\SingleResourceEndpoint;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Onboarding;

class OnboardingEndpoint extends EndpointAbstract implements SingleResourceEndpoint
{
    protected string $resourcePath = "onboarding/me";

    /**
     * @inheritDoc
     */
    protected function getResourceObject(): Onboarding
    {
        return new Onboarding($this->client);
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
        return $this->rest_read('', []);
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
        return $this->create($parameters, []);
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

<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Contracts\SingleResourceEndpointContract;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\Onboarding;

class OnboardingEndpoint extends RestEndpoint implements SingleResourceEndpointContract
{
    /**
     * The resource path.
     *
     * @var string
     */
    protected string $resourcePath = "onboarding/me";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Onboarding::class;

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
     * @deprecated 2023-05-01 For an alternative, see https://docs.mollie.com/reference/create-client-link .
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
        $this->createResource($body, $filters);
    }
}

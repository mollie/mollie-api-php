<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Factories\CreateClientLinkPayloadFactory;
use Mollie\Api\Http\Data\CreateClientLinkPayload;
use Mollie\Api\Http\Requests\CreateClientLinkRequest;
use Mollie\Api\Resources\ClientLink;

class ClientLinkEndpointCollection extends EndpointCollection
{
    /**
     * Creates a client link in Mollie.
     *
     * @param  array|CreateClientLinkPayload  $payload  An array containing details on the client link.
     *
     * @throws ApiException
     */
    public function create($payload = []): ClientLink
    {
        if (! $payload instanceof CreateClientLinkPayload) {
            $payload = CreateClientLinkPayloadFactory::new($payload)
                ->create();
        }

        /** @var ClientLink */
        return $this->send(new CreateClientLinkRequest($payload));
    }
}

<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Factories\CreateClientLinkPayloadFactory;
use Mollie\Api\Http\Payload\CreateClientLink;
use Mollie\Api\Http\Requests\CreateClientLinkRequest;
use Mollie\Api\Resources\ClientLink;

class ClientLinkEndpointCollection extends EndpointCollection
{
    /**
     * Creates a client link in Mollie.
     *
     * @param  array  $data  An array containing details on the client link.
     *
     * @throws ApiException
     */
    public function create($data = []): ClientLink
    {
        if (! $data instanceof CreateClientLink) {
            $data = CreateClientLinkPayloadFactory::new($data)
                ->create();
        }

        /** @var ClientLink */
        return $this->send(new CreateClientLinkRequest($data));
    }
}

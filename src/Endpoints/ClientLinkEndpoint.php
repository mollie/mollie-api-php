<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\ClientLink;

class ClientLinkEndpoint extends RestEndpoint
{
    protected string $resourcePath = "client-links";

    /**
     * @inheritDoc
     */
    public static function getResourceClass(): string
    {
        return  ClientLink::class;
    }

    /**
     * Creates a client link in Mollie.
     *
     * @param array $data An array containing details on the client link.
     *
     * @return ClientLink
     * @throws ApiException
     */
    public function create(array $data = []): ClientLink
    {
        /** @var ClientLink */
        return $this->createResource($data, []);
    }
}

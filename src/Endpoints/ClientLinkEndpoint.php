<?php

namespace Mollie\Api\Endpoints;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Resources\ClientLink;

class ClientLinkEndpoint extends RestEndpoint
{
    /**
     * The resource path.
     *
     * @var string
     */
    protected string $resourcePath = "client-links";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = ClientLink::class;

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

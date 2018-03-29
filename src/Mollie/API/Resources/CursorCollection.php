<?php

namespace Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;

abstract class CursorCollection extends BaseCollection
{
    /**
     * @var MollieApiClient
     */
    protected $client;

    /**
     * @param MollieApiClient $client
     * @param int $count
     * @param array $_links
     */
    public function __construct(MollieApiClient $client, $count, array $_links)
    {
        parent::__construct($count, $_links);

        $this->client = $client;
    }

    public function next()
    {
        // todo implement
    }

    public function previous()
    {
        // todo implement
    }
}
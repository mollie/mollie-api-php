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
     * @param object $_links
     */
    public function __construct(MollieApiClient $client, $count, $_links)
    {
        parent::__construct($count, $_links);

        $this->client = $client;
    }

    /**
     * Return the next set of resources when available
     *
     * @return CursorCollection|null
     */
    abstract public function next();

    /**
     * Return the previous set of resources when available
     *
     * @return CursorCollection|null
     */
    abstract public function previous();
}
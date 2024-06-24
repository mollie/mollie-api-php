<?php

namespace Mollie\Api\Resources;

abstract class BaseCollection extends \ArrayObject
{
    /**
     * Total number of retrieved objects.
     *
     * @var int
     */
    public int $count;

    /**
     * @var \stdClass|null
     */
    public ?\stdClass $_links;

    /**
     * @param int $count
     * @param \stdClass|null $_links
     */
    public function __construct(int $count, ?\stdClass $_links)
    {
        $this->count = $count;
        $this->_links = $_links;

        parent::__construct();
    }

    /**
     * @return string|null
     */
    abstract public function getCollectionResourceName();
}

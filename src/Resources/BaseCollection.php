<?php

namespace Mollie\Api\Resources;

abstract class BaseCollection extends \ArrayObject
{
    /**
     * Total number of retrieved objects.
     *
     * @var int
     */
    public $count;

    /**
     * @var object[]
     */
    public $_links;

    /**
     * @param int $count
     * @param object[] $_links
     */
    public function __construct($count, $_links)
    {
        $this->count = $count;
        $this->_links = $_links;
    }

    /**
     * @return string
     */
    abstract public function getCollectionResourceName();
}

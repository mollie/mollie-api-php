<?php

namespace Mollie\Api\Resources;

class CustomerCollection extends CursorCollection
{
    /**
     * @return string
     */
    public static function getCollectionResourceName(): string
    {
        return "customers";
    }

    /**
     * @return string
     */
    public static function getResourceClass(): string
    {
        return Customer::class;
    }
}

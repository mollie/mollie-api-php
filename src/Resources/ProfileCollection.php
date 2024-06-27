<?php

namespace Mollie\Api\Resources;

class ProfileCollection extends CursorCollection
{
    /**
     * @return string
     */
    public function getCollectionResourceName(): string
    {
        return "profiles";
    }

    /**
     * @return Profile
     */
    protected function createResourceObject(): Profile
    {
        return new Profile($this->client);
    }
}

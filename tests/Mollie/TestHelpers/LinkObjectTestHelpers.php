<?php

namespace Tests\Mollie\TestHelpers;

trait LinkObjectTestHelpers
{
    protected function assertLinkObject($href, $type, $linkObject)
    {
        return $this->assertEquals(
            $this->createLinkObject($href, $type),
            $linkObject
        );
    }

    protected function createLinkObject($href, $type)
    {
        return (object) [
            'href' => $href,
            'type' => $type,
        ];
    }
}

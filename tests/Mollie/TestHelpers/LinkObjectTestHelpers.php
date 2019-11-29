<?php

namespace Tests\Mollie\TestHelpers;

trait LinkObjectTestHelpers
{
    protected function assertLinkObject($href, $type, $linkObject)
    {
        $this->assertEquals(
            $this->createLinkObject($href, $type),
            $linkObject
        );
    }

    protected function createNamedLinkObject($name, $href, $type)
    {
        return (object) [
            $name => $this->createLinkObject($href, $type),
        ];
    }

    protected function createLinkObject($href, $type)
    {
        return (object) [
            'href' => $href,
            'type' => $type,
        ];
    }
}

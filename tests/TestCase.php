<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Tests\Fixtures\MockClient;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        MockClient::setAutoHydrate(false);
    }
}

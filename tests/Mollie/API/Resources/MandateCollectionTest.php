<?php

namespace Tests\Mollie\API\Resources;

use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Mandate;
use Mollie\Api\Resources\MandateCollection;
use Mollie\Api\Types\MandateStatus;
use PHPUnit\Framework\TestCase;

class MandateCollectionTest extends TestCase
{
    /**
     * @var \Mollie\Api\MollieApiClient
     */
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createMock(MollieApiClient::class);
    }

    public function testWhereStatus()
    {
        $collection = new MandateCollection($this->client, [
            $this->getMandateWithStatus(MandateStatus::VALID),
            $this->getMandateWithStatus(MandateStatus::VALID),
            $this->getMandateWithStatus(MandateStatus::VALID),
            $this->getMandateWithStatus(MandateStatus::INVALID),
            $this->getMandateWithStatus(MandateStatus::INVALID),
            $this->getMandateWithStatus(MandateStatus::PENDING),
        ], null);

        $valid = $collection->whereStatus(MandateStatus::VALID);
        $invalid = $collection->whereStatus(MandateStatus::INVALID);
        $pending = $collection->whereStatus(MandateStatus::PENDING);

        $this->assertInstanceOf(MandateCollection::class, $collection);
        $this->assertInstanceOf(MandateCollection::class, $valid);
        $this->assertInstanceOf(MandateCollection::class, $invalid);
        $this->assertInstanceOf(MandateCollection::class, $pending);

        $this->assertCount(6, $collection);
        $this->assertEquals(6, $collection->count());
        $this->assertCount(3, $valid);
        $this->assertEquals(3, $valid->count());
        $this->assertCount(2, $invalid);
        $this->assertEquals(2, $invalid->count());
        $this->assertCount(1, $pending);
        $this->assertEquals(1, $pending->count());
    }

    /**
     * @param string $status
     * @return \Mollie\Api\Resources\Mandate
     */
    protected function getMandateWithStatus($status)
    {
        $mandate = new Mandate($this->client);
        $mandate->status = $status;

        return $mandate;
    }
}

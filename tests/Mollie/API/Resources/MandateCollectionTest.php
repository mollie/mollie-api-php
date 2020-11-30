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
        $collection = new MandateCollection($this->client, 3, null);
        $collection[] = $this->getMandateWithStatus(MandateStatus::STATUS_VALID);
        $collection[] = $this->getMandateWithStatus(MandateStatus::STATUS_VALID);
        $collection[] = $this->getMandateWithStatus(MandateStatus::STATUS_VALID);
        $collection[] = $this->getMandateWithStatus(MandateStatus::STATUS_INVALID);
        $collection[] = $this->getMandateWithStatus(MandateStatus::STATUS_INVALID);
        $collection[] = $this->getMandateWithStatus(MandateStatus::STATUS_PENDING);

        $valid = $collection->whereStatus(MandateStatus::STATUS_VALID);
        $invalid = $collection->whereStatus(MandateStatus::STATUS_INVALID);
        $pending = $collection->whereStatus(MandateStatus::STATUS_PENDING);

        $this->assertInstanceOf(MandateCollection::class, $collection);
        $this->assertInstanceOf(MandateCollection::class, $valid);
        $this->assertInstanceOf(MandateCollection::class, $invalid);
        $this->assertInstanceOf(MandateCollection::class, $pending);

        $this->assertCount(6, $collection);
        $this->assertCount(3, $valid);
        $this->assertCount(2, $invalid);
        $this->assertCount(1, $pending);
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

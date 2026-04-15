<?php

declare(strict_types=1);

namespace Tests\Resources;

use Mollie\Api\Http\Response;
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

    public function test_where_status()
    {
        $collection = new MandateCollection($this->client, [
            $this->getMandateWithStatus(MandateStatus::Valid->value),
            $this->getMandateWithStatus(MandateStatus::Valid->value),
            $this->getMandateWithStatus(MandateStatus::Valid->value),
            $this->getMandateWithStatus(MandateStatus::Invalid->value),
            $this->getMandateWithStatus(MandateStatus::Invalid->value),
            $this->getMandateWithStatus(MandateStatus::Pending->value),
        ]);

        $response = $this->createMock(Response::class);

        $collection->setResponse($response);

        $valid = $collection->whereStatus(MandateStatus::Valid->value);
        $invalid = $collection->whereStatus(MandateStatus::Invalid->value);
        $pending = $collection->whereStatus(MandateStatus::Pending->value);

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
     * @param  string  $status
     * @return \Mollie\Api\Resources\Mandate
     */
    protected function getMandateWithStatus($status)
    {
        $mandate = new Mandate(
            $this->createMock(MollieApiClient::class),
        );
        $mandate->status = $status;

        return $mandate;
    }
}

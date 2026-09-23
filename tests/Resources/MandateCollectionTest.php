<?php

declare(strict_types=1);

namespace Tests\Resources;

use Mollie\Api\Http\Response;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Mandate;
use Mollie\Api\Resources\MandateCollection;
use Mollie\Api\Resources\ResourceHydrator;
use Mollie\Api\Types\MandateStatus;
use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    public function where_status()
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

    #[Test]
    public function where_status_matches_hydrated_mandates_by_case_and_by_raw_string(): void
    {
        $valid = $this->hydrateMandate(['status' => 'valid']);
        $this->assertSame(MandateStatus::Valid, $valid->status, 'precondition: hydration yields the enum case');

        $collection = new MandateCollection($this->client, [
            $valid,
            $this->hydrateMandate(['status' => 'valid']),
            $this->hydrateMandate(['status' => 'invalid']),
        ]);
        $collection->setResponse($this->createMock(Response::class));

        $this->assertCount(2, $collection->whereStatus(MandateStatus::Valid));
        $this->assertCount(2, $collection->whereStatus('valid'));
        $this->assertCount(1, $collection->whereStatus(MandateStatus::Invalid));
        $this->assertCount(1, $collection->whereStatus('invalid'));
        $this->assertCount(0, $collection->whereStatus('status-from-the-future'));
    }

    private function hydrateMandate(array $data): Mandate
    {
        $mandate = new Mandate($this->createMock(MollieApiClient::class));
        (new ResourceHydrator)->hydrate($mandate, ['resource' => 'mandate', 'id' => 'mdt_h3gAaD5zP'] + $data, $this->createMock(Response::class));

        return $mandate;
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

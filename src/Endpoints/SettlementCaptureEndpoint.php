<?php

declare(strict_types=1);

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\Capture;
use Mollie\Api\Resources\CaptureCollection;
use Mollie\Api\Resources\LazyCollection;

class SettlementCaptureEndpoint extends EndpointCollection
{
    /**
     * The resource path.
     *
     * @var string
     */
    protected string $resourcePath = "settlements_captures";

    /**
     * Resource class name.
     *
     * @var string
     */
    public static string $resource = Capture::class;

    /**
     * The resource collection class name.
     *
     * @var string
     */
    public static string $resourceCollection = CaptureCollection::class;

    /**
     * Retrieves a collection of Settlement Captures from Mollie.
     *
     * @param string $settlementId
     * @param string|null $from The first capture ID you want to include in your list.
     * @param int|null $limit
     * @param array $parameters
     *
     * @return CaptureCollection
     * @throws \Mollie\Api\Exceptions\ApiException
     */
    public function pageForId(string $settlementId, ?string $from = null, ?int $limit = null, array $parameters = []): CaptureCollection
    {
        $this->parentId = $settlementId;

        /** @var CaptureCollection */
        return $this->fetchCollection($from, $limit, $parameters);
    }

    /**
     * Create an iterator for iterating over captures for the given settlement id, retrieved from Mollie.
     *
     * @param string $settlementId
     * @param string $from The first resource ID you want to include in your list.
     * @param int $limit
     * @param array $parameters
     * @param bool $iterateBackwards Set to true for reverse order iteration (default is false).
     *
     * @return LazyCollection
     */
    public function iteratorForId(
        string $settlementId,
        ?string $from = null,
        ?int $limit = null,
        array $parameters = [],
        bool $iterateBackwards = false
    ): LazyCollection {
        $this->parentId = $settlementId;

        return $this->createIterator($from, $limit, $parameters, $iterateBackwards);
    }
}

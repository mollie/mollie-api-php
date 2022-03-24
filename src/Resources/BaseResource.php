<?php

namespace Mollie\Api\Resources;

use Mollie\Api\MollieApiClient;

/**
 * @property string $resource
 * @property int $id
 * @property string $mode
 * @property string|null $createdAt
 * @property \stdClass $amount
 * @property string|null $startDate
 * @property string|null $releaseDate
 * @property string $name
 * @property string $website
 * @property string $email
 * @property string $phone
 * @property int|null $categoryCode
 * @property string $status
 * @property \stdClass $review
 * @property \stdClass $_links
 * @property \stdClass $minimumAmount
 * @property \stdClass $maximumAmount
 * @property \stdClass $settlementAmount
 * @property \stdClass $image
 * @property string  $description
 * @property array|object[] $pricing
 * @property string $paymentId
 * @property string $redirectUrl
 * @property string $webhookUrl
 * @property \stdClass|mixed|null $metadata
 * @property string|null $sequenceType
 * @property string $profileId
 * @property \stdClass|null $details
 * @method issuers()
 * @method pricing()
 */

abstract class BaseResource
{
    /**
     * @var MollieApiClient
     */
    protected $client;

    /**
     * @param MollieApiClient $client
     */
    public function __construct(MollieApiClient $client)
    {
        $this->client = $client;
    }
}

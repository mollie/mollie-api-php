<?php

declare(strict_types=1);

namespace Mollie\Api\Endpoints;

use Mollie\Api\Resources\Payment;
use Mollie\Api\Resources\PaymentCollection;
use Mollie\Api\Resources\PaymentLink;

class PaymentLinkPaymentEndpoint extends CollectionEndpointAbstract
{
    protected $resourcePath = 'payment-links_payments';

    /**
     * @inheritDoc
     */
    protected function getResourceCollectionObject($count, $_links)
    {
        return new PaymentCollection($this->client, $count, $_links);
    }

    /**
     * @inheritDoc
     */
    protected function getResourceObject()
    {
        return new Payment($this->client);
    }

    public function pageForId(string $paymentLinkId, string $from = null, int $limit = null, array $filters = [])
    {
        $this->parentId = $paymentLinkId;

        return $this->rest_list($from, $limit, $filters);
    }

    public function pageFor(PaymentLink $paymentLink, string $from = null, int $limit = null, array $filters = [])
    {
        return $this->pageForId($paymentLink->id, $from, $limit, $filters);
    }
}
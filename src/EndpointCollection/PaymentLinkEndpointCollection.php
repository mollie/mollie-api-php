<?php

namespace Mollie\Api\EndpointCollection;

use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Factories\CreatePaymentLinkPayloadFactory;
use Mollie\Api\Factories\PaginatedQueryFactory;
use Mollie\Api\Factories\UpdatePaymentLinkPayloadFactory;
use Mollie\Api\Http\Data\CreatePaymentLinkPayload;
use Mollie\Api\Http\Data\UpdatePaymentLinkPayload;
use Mollie\Api\Http\Requests\CreatePaymentLinkRequest;
use Mollie\Api\Http\Requests\DeletePaymentLinkRequest;
use Mollie\Api\Http\Requests\GetPaginatedPaymentLinksRequest;
use Mollie\Api\Http\Requests\GetPaymentLinkRequest;
use Mollie\Api\Http\Requests\UpdatePaymentLinkRequest;
use Mollie\Api\Resources\LazyCollection;
use Mollie\Api\Resources\PaymentLink;
use Mollie\Api\Resources\PaymentLinkCollection;
use Mollie\Api\Utils\Utility;

class PaymentLinkEndpointCollection extends EndpointCollection
{
    /**
     * Creates a payment link in Mollie.
     *
     * @param  array|CreatePaymentLinkPayload  $payload  An array containing details on the payment link.
     *
     * @throws ApiException
     */
    public function create($payload = []): PaymentLink
    {
        if (! $payload instanceof CreatePaymentLinkPayload) {
            $payload = CreatePaymentLinkPayloadFactory::new($payload)->create();
        }

        /** @var PaymentLink */
        return $this->send(new CreatePaymentLinkRequest($payload));
    }

    /**
     * Retrieve a payment link from Mollie.
     *
     * Will throw an ApiException if the payment link id is invalid or the resource cannot be found.
     *
     * @param  array  $testmode
     *
     * @throws ApiException
     */
    public function get(string $paymentLinkId, $testmode = []): PaymentLink
    {
        $testmode = Utility::extractBool($testmode, 'testmode', false);

        /** @var PaymentLink */
        return $this->send((new GetPaymentLinkRequest($paymentLinkId))->test($testmode));
    }

    /**
     * Update a Payment Link.
     *
     * @param  array|UpdatePaymentLinkPayload  $payload
     *
     * @throws ApiException
     */
    public function update(string $paymentLinkId, $payload = [], bool $testmode = false): PaymentLink
    {
        if (! $payload instanceof UpdatePaymentLinkPayload) {
            $payload = UpdatePaymentLinkPayloadFactory::new($payload)->create();
        }

        /** @var PaymentLink */
        return $this->send((new UpdatePaymentLinkRequest($paymentLinkId, $payload))->test($testmode));
    }

    /**
     * Delete a Payment Link.
     *
     *
     * @throws ApiException
     */
    public function delete(string $paymentLinkId, bool $testmode = false): void
    {
        $this->send((new DeletePaymentLinkRequest($paymentLinkId))->test($testmode));
    }

    /**
     * Retrieves a collection of Payment Links from Mollie.
     *
     * @param  string|null  $from  The first payment link ID you want to include in your list.
     * @param  array|bool  $testmode
     *
     * @throws ApiException
     */
    public function page(?string $from = null, ?int $limit = null, $testmode = []): PaymentLinkCollection
    {
        $testmode = Utility::extractBool($testmode, 'testmode', false);

        $query = PaginatedQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
        ])->create();

        /** @var PaymentLinkCollection */
        return $this->send((new GetPaginatedPaymentLinksRequest($query))->test($testmode));
    }

    /**
     * Create an iterator for iterating over payment links retrieved from Mollie.
     *
     * @param  string|null  $from  The first resource ID you want to include in your list.
     * @param  bool  $iterateBackwards  Set to true for reverse order iteration (default is false).
     */
    public function iterator(
        ?string $from = null,
        ?int $limit = null,
        bool $testmode = false,
        bool $iterateBackwards = false
    ): LazyCollection {
        $testmode = Utility::extractBool($testmode, 'testmode', false);
        $query = PaginatedQueryFactory::new([
            'from' => $from,
            'limit' => $limit,
        ])->create();

        return $this->send(
            (new GetPaginatedPaymentLinksRequest($query))
                ->useIterator()
                ->setIterationDirection($iterateBackwards)
                ->test($testmode)
        );
    }
}

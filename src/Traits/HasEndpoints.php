<?php

namespace Mollie\Api\Traits;

use Mollie\Api\EndpointCollection\BalanceEndpointCollection;
use Mollie\Api\EndpointCollection\ChargebackEndpointCollection;
use Mollie\Api\EndpointCollection\ClientEndpointCollection;
use Mollie\Api\EndpointCollection\ClientLinkEndpointCollection;
use Mollie\Api\EndpointCollection\CustomerEndpointCollection;
use Mollie\Api\EndpointCollection\OrganizationEndpointCollection;
use Mollie\Api\EndpointCollection\PaymentEndpointCollection;
use Mollie\Api\EndpointCollection\PaymentRefundEndpointCollection;
use Mollie\Api\Endpoints\CustomerPaymentsEndpoint;
use Mollie\Api\Endpoints\InvoiceEndpoint;
use Mollie\Api\Endpoints\MandateEndpoint;
use Mollie\Api\Endpoints\MethodEndpoint;
use Mollie\Api\Endpoints\MethodIssuerEndpoint;
use Mollie\Api\Endpoints\OnboardingEndpoint;
use Mollie\Api\Endpoints\OrderEndpoint;
use Mollie\Api\Endpoints\OrderLineEndpoint;
use Mollie\Api\Endpoints\OrderPaymentEndpoint;
use Mollie\Api\Endpoints\OrderRefundEndpoint;
use Mollie\Api\Endpoints\OrderShipmentEndpoint;
use Mollie\Api\Endpoints\OrganizationPartnerEndpoint;
use Mollie\Api\Endpoints\PaymentCaptureEndpoint;
use Mollie\Api\Endpoints\PaymentChargebackEndpoint;
use Mollie\Api\Endpoints\PaymentLinkEndpoint;
use Mollie\Api\Endpoints\PaymentLinkPaymentEndpoint;
use Mollie\Api\Endpoints\PaymentRouteEndpoint;
use Mollie\Api\Endpoints\PermissionEndpoint;
use Mollie\Api\Endpoints\ProfileEndpoint;
use Mollie\Api\Endpoints\ProfileMethodEndpoint;
use Mollie\Api\Endpoints\RefundEndpoint;
use Mollie\Api\Endpoints\SettlementCaptureEndpoint;
use Mollie\Api\Endpoints\SettlementChargebackEndpoint;
use Mollie\Api\Endpoints\SettlementPaymentEndpoint;
use Mollie\Api\Endpoints\SettlementRefundEndpoint;
use Mollie\Api\Endpoints\SettlementsEndpoint;
use Mollie\Api\Endpoints\SubscriptionEndpoint;
use Mollie\Api\Endpoints\SubscriptionPaymentEndpoint;
use Mollie\Api\Endpoints\TerminalEndpoint;
use Mollie\Api\Endpoints\WalletEndpoint;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\BalanceTransactionCollection;

/**
 * @mixin MollieApiClient
 */
trait HasEndpoints
{
    protected string $apiEndpoint = MollieApiClient::API_ENDPOINT;

    protected static array $endpoints = [];

    protected function initializeHasEndpoints(): void
    {
        if (! empty(static::$endpoints)) {
            return;
        }

        $endpointClasses = [
            'balances' => BalanceEndpointCollection::class,
            'balanceReports' => BalanceEndpointCollection::class,
            'balanceTransactions' => BalanceTransactionCollection::class,
            'chargebacks' => ChargebackEndpointCollection::class,
            'clients' => ClientEndpointCollection::class,
            'clientLinks' => ClientLinkEndpointCollection::class,
            'customerPayments' => CustomerPaymentsEndpoint::class,
            'customers' => CustomerEndpointCollection::class,
            'invoices' => InvoiceEndpoint::class,
            'mandates' => MandateEndpoint::class,
            'methods' => MethodEndpoint::class,
            'methodIssuers' => MethodIssuerEndpoint::class,
            'onboarding' => OnboardingEndpoint::class,
            'orderLines' => OrderLineEndpoint::class,
            'orderPayments' => OrderPaymentEndpoint::class,
            'orderRefunds' => OrderRefundEndpoint::class,
            'orders' => OrderEndpoint::class,
            'organizationPartners' => OrganizationPartnerEndpoint::class,
            'organizations' => OrganizationEndpointCollection::class,
            'payments' => PaymentEndpointCollection::class,
            'paymentRefunds' => PaymentRefundEndpointCollection::class,
            'paymentCaptures' => PaymentCaptureEndpoint::class,
            'paymentChargebacks' => PaymentChargebackEndpoint::class,
            'paymentLinks' => PaymentLinkEndpoint::class,
            'paymentLinkPayments' => PaymentLinkPaymentEndpoint::class,
            'paymentRoutes' => PaymentRouteEndpoint::class,
            'permissions' => PermissionEndpoint::class,
            'profiles' => ProfileEndpoint::class,
            'profileMethods' => ProfileMethodEndpoint::class,
            'refunds' => RefundEndpoint::class,
            'settlementCaptures' => SettlementCaptureEndpoint::class,
            'settlementChargebacks' => SettlementChargebackEndpoint::class,
            'settlementPayments' => SettlementPaymentEndpoint::class,
            'settlementRefunds' => SettlementRefundEndpoint::class,
            'shipments' => OrderShipmentEndpoint::class,
            'settlements' => SettlementsEndpoint::class,
            'subscriptions' => SubscriptionEndpoint::class,
            'subscriptionPayments' => SubscriptionPaymentEndpoint::class,
            'terminals' => TerminalEndpoint::class,
            'wallets' => WalletEndpoint::class,
        ];

        foreach ($endpointClasses as $name => $class) {
            static::$endpoints[$name] = $class;
        }
    }

    /**
     * @param  string  $url
     */
    public function setApiEndpoint($url): self
    {
        $this->apiEndpoint = rtrim(trim($url), '/');

        return $this;
    }

    public function getApiEndpoint(): string
    {
        return $this->apiEndpoint;
    }

    /**
     * Magic getter to access the endpoints.
     *
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function __get(string $name)
    {
        if (isset(static::$endpoints[$name])) {
            return new static::$endpoints[$name]($this);
        }

        throw new \Exception("Undefined endpoint: $name");
    }
}

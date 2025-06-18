<?php

namespace Mollie\Api\Traits;

use Mollie\Api\EndpointCollection\BalanceEndpointCollection;
use Mollie\Api\EndpointCollection\BalanceReportEndpointCollection;
use Mollie\Api\EndpointCollection\BalanceTransactionEndpointCollection;
use Mollie\Api\EndpointCollection\CapabilityEndpointCollection;
use Mollie\Api\EndpointCollection\ChargebackEndpointCollection;
use Mollie\Api\EndpointCollection\ClientEndpointCollection;
use Mollie\Api\EndpointCollection\ClientLinkEndpointCollection;
use Mollie\Api\EndpointCollection\CustomerEndpointCollection;
use Mollie\Api\EndpointCollection\CustomerPaymentsEndpointCollection;
use Mollie\Api\EndpointCollection\InvoiceEndpointCollection;
use Mollie\Api\EndpointCollection\MandateEndpointCollection;
use Mollie\Api\EndpointCollection\MethodEndpointCollection;
use Mollie\Api\EndpointCollection\MethodIssuerEndpointCollection;
use Mollie\Api\EndpointCollection\OnboardingEndpointCollection;
use Mollie\Api\EndpointCollection\OrganizationEndpointCollection;
use Mollie\Api\EndpointCollection\OrganizationPartnerEndpointCollection;
use Mollie\Api\EndpointCollection\PaymentCaptureEndpointCollection;
use Mollie\Api\EndpointCollection\PaymentChargebackEndpointCollection;
use Mollie\Api\EndpointCollection\PaymentEndpointCollection;
use Mollie\Api\EndpointCollection\PaymentLinkEndpointCollection;
use Mollie\Api\EndpointCollection\PaymentLinkPaymentEndpointCollection;
use Mollie\Api\EndpointCollection\PaymentRefundEndpointCollection;
use Mollie\Api\EndpointCollection\PaymentRouteEndpointCollection;
use Mollie\Api\EndpointCollection\PermissionEndpointCollection;
use Mollie\Api\EndpointCollection\ProfileEndpointCollection;
use Mollie\Api\EndpointCollection\ProfileMethodEndpointCollection;
use Mollie\Api\EndpointCollection\RefundEndpointCollection;
use Mollie\Api\EndpointCollection\SalesInvoiceEndpointCollection;
use Mollie\Api\EndpointCollection\SessionEndpointCollection;
use Mollie\Api\EndpointCollection\SettlementCaptureEndpointCollection;
use Mollie\Api\EndpointCollection\SettlementChargebackEndpointCollection;
use Mollie\Api\EndpointCollection\SettlementEndpointCollection;
use Mollie\Api\EndpointCollection\SettlementPaymentEndpointCollection;
use Mollie\Api\EndpointCollection\SettlementRefundEndpointCollection;
use Mollie\Api\EndpointCollection\SubscriptionEndpointCollection;
use Mollie\Api\EndpointCollection\SubscriptionPaymentEndpointCollection;
use Mollie\Api\EndpointCollection\TerminalEndpointCollection;
use Mollie\Api\EndpointCollection\WalletEndpointCollection;
use Mollie\Api\EndpointCollection\WebhookEndpointCollection;
use Mollie\Api\EndpointCollection\WebhookEventEndpointCollection;
use Mollie\Api\MollieApiClient;

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
            'balanceReports' => BalanceReportEndpointCollection::class,
            'balanceTransactions' => BalanceTransactionEndpointCollection::class,
            'capabilities' => CapabilityEndpointCollection::class,
            'chargebacks' => ChargebackEndpointCollection::class,
            'clients' => ClientEndpointCollection::class,
            'clientLinks' => ClientLinkEndpointCollection::class,
            'customerPayments' => CustomerPaymentsEndpointCollection::class,
            'customers' => CustomerEndpointCollection::class,
            'invoices' => InvoiceEndpointCollection::class,
            'mandates' => MandateEndpointCollection::class,
            'methods' => MethodEndpointCollection::class,
            'methodIssuers' => MethodIssuerEndpointCollection::class,
            'onboarding' => OnboardingEndpointCollection::class,
            'organizationPartners' => OrganizationPartnerEndpointCollection::class,
            'organizations' => OrganizationEndpointCollection::class,
            'payments' => PaymentEndpointCollection::class,
            'paymentRefunds' => PaymentRefundEndpointCollection::class,
            'paymentCaptures' => PaymentCaptureEndpointCollection::class,
            'paymentChargebacks' => PaymentChargebackEndpointCollection::class,
            'paymentLinks' => PaymentLinkEndpointCollection::class,
            'paymentLinkPayments' => PaymentLinkPaymentEndpointCollection::class,
            'paymentRoutes' => PaymentRouteEndpointCollection::class,
            'permissions' => PermissionEndpointCollection::class,
            'profiles' => ProfileEndpointCollection::class,
            'profileMethods' => ProfileMethodEndpointCollection::class,
            'refunds' => RefundEndpointCollection::class,
            'salesInvoices' => SalesInvoiceEndpointCollection::class,
            'sessions' => SessionEndpointCollection::class,
            'settlementCaptures' => SettlementCaptureEndpointCollection::class,
            'settlementChargebacks' => SettlementChargebackEndpointCollection::class,
            'settlementPayments' => SettlementPaymentEndpointCollection::class,
            'settlementRefunds' => SettlementRefundEndpointCollection::class,
            'settlements' => SettlementEndpointCollection::class,
            'subscriptions' => SubscriptionEndpointCollection::class,
            'subscriptionPayments' => SubscriptionPaymentEndpointCollection::class,
            'terminals' => TerminalEndpointCollection::class,
            'wallets' => WalletEndpointCollection::class,
            'webhooks' => WebhookEndpointCollection::class,
            'webhookEvents' => WebhookEventEndpointCollection::class,
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

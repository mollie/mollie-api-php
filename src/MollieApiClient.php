<?php

namespace Mollie\Api;

use Mollie\Api\Contracts\Connector;
use Mollie\Api\Contracts\HttpAdapterContract;
use Mollie\Api\Contracts\IdempotencyKeyGeneratorContract;
use Mollie\Api\Contracts\MollieHttpAdapterPickerContract;
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
use Mollie\Api\Fake\MockMollieClient;
use Mollie\Api\Http\Adapter\MollieHttpAdapterPicker;
use Mollie\Api\Idempotency\DefaultIdempotencyKeyGenerator;
use Mollie\Api\Traits\HandlesAuthentication;
use Mollie\Api\Traits\HandlesDebugging;
use Mollie\Api\Traits\HandlesIdempotency;
use Mollie\Api\Traits\HandlesTestmode;
use Mollie\Api\Traits\HandlesVersions;
use Mollie\Api\Traits\HasEndpoints;
use Mollie\Api\Traits\HasMiddleware;
use Mollie\Api\Traits\HasRequestProperties;
use Mollie\Api\Traits\Initializable;
use Mollie\Api\Traits\SendsRequests;
use Mollie\Api\Utils\Url;

/**
 * @property BalanceEndpointCollection $balances
 * @property BalanceReportEndpointCollection $balanceReports
 * @property BalanceTransactionEndpointCollection $balanceTransactions
 * @property ChargebackEndpointCollection $chargebacks
 * @property CapabilityEndpointCollection $capabilities
 * @property ClientEndpointCollection $clients
 * @property ClientLinkEndpointCollection $clientLinks
 * @property CustomerPaymentsEndpointCollection $customerPayments
 * @property CustomerEndpointCollection $customers
 * @property InvoiceEndpointCollection $invoices
 * @property MandateEndpointCollection $mandates
 * @property MethodEndpointCollection $methods
 * @property MethodIssuerEndpointCollection $methodIssuers
 * @property OnboardingEndpointCollection $onboarding
 * @property OrganizationEndpointCollection $organizations
 * @property OrganizationPartnerEndpointCollection $organizationPartners
 * @property PaymentEndpointCollection $payments
 * @property PaymentCaptureEndpointCollection $paymentCaptures
 * @property PaymentChargebackEndpointCollection $paymentChargebacks
 * @property PaymentLinkEndpointCollection $paymentLinks
 * @property PaymentLinkPaymentEndpointCollection $paymentLinkPayments
 * @property PaymentRefundEndpointCollection $paymentRefunds
 * @property PaymentRouteEndpointCollection $paymentRoutes
 * @property PermissionEndpointCollection $permissions
 * @property ProfileEndpointCollection $profiles
 * @property ProfileMethodEndpointCollection $profileMethods
 * @property RefundEndpointCollection $refunds
 * @property SalesInvoiceEndpointCollection $salesInvoices
 * @property SessionEndpointCollection $sessions
 * @property SettlementCaptureEndpointCollection $settlementCaptures
 * @property SettlementChargebackEndpointCollection $settlementChargebacks
 * @property SettlementEndpointCollection $settlements
 * @property SettlementPaymentEndpointCollection $settlementPayments
 * @property SettlementRefundEndpointCollection $settlementRefunds
 * @property SubscriptionEndpointCollection $subscriptions
 * @property SubscriptionPaymentEndpointCollection $subscriptionPayments
 * @property TerminalEndpointCollection $terminals
 * @property WalletEndpointCollection $wallets
 * @property WebhookEndpointCollection $webhooks
 * @property WebhookEventEndpointCollection $webhookEvents
 * @property HttpAdapterContract $httpClient
 */
class MollieApiClient implements Connector
{
    use HandlesAuthentication;
    use HandlesDebugging;
    use HandlesIdempotency;
    use HandlesTestmode;
    use HandlesVersions;
    use HasEndpoints;
    use HasMiddleware;
    use HasRequestProperties;
    use Initializable;
    use SendsRequests;

    /**
     * Version of our client.
     */
    public const CLIENT_VERSION = '3.4.0';

    /**
     * Endpoint of the remote API.
     */
    public const API_ENDPOINT = 'https://api.mollie.com';

    /**
     * Version of the remote API.
     */
    public const API_VERSION = 'v2';

    /**
     * Http client used to perform requests.
     */
    protected HttpAdapterContract $httpClient;

    /**
     * @param  \GuzzleHttp\ClientInterface|\Mollie\Api\Contracts\HttpAdapterContract|null  $client
     *
     * @throws \Mollie\Api\Exceptions\IncompatiblePlatformException|\Mollie\Api\Exceptions\UnrecognizedClientException
     */
    public function __construct(
        $client = null,
        ?MollieHttpAdapterPickerContract $adapterPicker = null,
        ?IdempotencyKeyGeneratorContract $idempotencyKeyGenerator = null
    ) {
        $adapterPicker = $adapterPicker ?: new MollieHttpAdapterPicker;
        $this->httpClient = $adapterPicker->pickHttpAdapter($client);

        CompatibilityChecker::make()->checkCompatibility();

        $this->idempotencyKeyGenerator = $idempotencyKeyGenerator ?? new DefaultIdempotencyKeyGenerator;

        $this->initializeTraits();
    }

    protected function defaultHeaders(): array
    {
        return [
            'X-Mollie-Client-Info' => php_uname(),
            'Accept' => 'application/json',
        ];
    }

    public function getHttpClient(): HttpAdapterContract
    {
        return $this->httpClient;
    }

    public function resolveBaseUrl(): string
    {
        return Url::join($this->apiEndpoint, self::API_VERSION);
    }

    public static function fake(array $expectedResponses = []): MockMollieClient
    {
        return new MockMollieClient($expectedResponses);
    }

    public function __serialize(): array
    {
        return [
            'apiEndpoint' => $this->apiEndpoint,
            'httpClient' => $this->httpClient,
            'idempotencyKeyGenerator' => $this->idempotencyKeyGenerator,
            'testmode' => $this->testmode,
            'versionStrings' => $this->versionStrings,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->apiEndpoint = $data['apiEndpoint'];
        $this->httpClient = $data['httpClient'];
        $this->idempotencyKeyGenerator = $data['idempotencyKeyGenerator'];
        $this->testmode = $data['testmode'];
        $this->versionStrings = $data['versionStrings'];
    }
}

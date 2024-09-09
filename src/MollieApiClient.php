<?php

namespace Mollie\Api;

use Mollie\Api\Contracts\Connector;
use Mollie\Api\Contracts\HttpAdapterContract;
use Mollie\Api\Contracts\IdempotencyKeyGeneratorContract;
use Mollie\Api\Contracts\MollieHttpAdapterPickerContract;
use Mollie\Api\EndpointCollection\BalanceEndpointCollection;
use Mollie\Api\EndpointCollection\BalanceReportEndpointCollection;
use Mollie\Api\EndpointCollection\ChargebackEndpointCollection;
use Mollie\Api\EndpointCollection\ClientEndpointCollection;
use Mollie\Api\EndpointCollection\ClientLinkEndpointCollection;
use Mollie\Api\EndpointCollection\CustomerEndpointCollection;
use Mollie\Api\EndpointCollection\OrderEndpointCollection;
use Mollie\Api\EndpointCollection\OrganizationEndpointCollection;
use Mollie\Api\EndpointCollection\PaymentEndpointCollection;
use Mollie\Api\EndpointCollection\PaymentRefundEndpointCollection;
use Mollie\Api\Endpoints\CustomerPaymentsEndpoint;
use Mollie\Api\Endpoints\InvoiceEndpoint;
use Mollie\Api\Endpoints\MandateEndpoint;
use Mollie\Api\Endpoints\MethodEndpoint;
use Mollie\Api\Endpoints\MethodIssuerEndpoint;
use Mollie\Api\Endpoints\OnboardingEndpoint;
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
use Mollie\Api\Http\Adapter\MollieHttpAdapterPicker;
use Mollie\Api\Idempotency\DefaultIdempotencyKeyGenerator;
use Mollie\Api\Resources\BalanceTransactionCollection;
use Mollie\Api\Traits\HandlesAuthentication;
use Mollie\Api\Traits\HandlesAutoHydration;
use Mollie\Api\Traits\HandlesDebugging;
use Mollie\Api\Traits\HandlesIdempotency;
use Mollie\Api\Traits\HandlesTestmode;
use Mollie\Api\Traits\HandlesVersions;
use Mollie\Api\Traits\HasEndpoints;
use Mollie\Api\Traits\HasMiddleware;
use Mollie\Api\Traits\HasRequestProperties;
use Mollie\Api\Traits\Initializable;
use Mollie\Api\Traits\SendsRequests;

/**
 * @property BalanceEndpointCollection $balances
 * @property BalanceReportEndpointCollection $balanceReports
 * @property BalanceTransactionCollection $balanceTransactions
 * @property ChargebackEndpointCollection $chargebacks
 * @property ClientEndpointCollection $clients
 * @property ClientLinkEndpointCollection $clientLinks
 * @property CustomerPaymentsEndpoint $customerPayments
 * @property CustomerEndpointCollection $customers
 * @property InvoiceEndpoint $invoices
 * @property MandateEndpoint $mandates
 * @property MethodEndpoint $methods
 * @property MethodIssuerEndpoint $methodIssuers
 * @property OnboardingEndpoint $onboarding
 * @property OrderEndpointCollection $orders
 * @property OrderLineEndpoint $orderLines
 * @property OrderPaymentEndpoint $orderPayments
 * @property OrderRefundEndpoint $orderRefunds
 * @property OrganizationEndpointCollection $organizations
 * @property OrganizationPartnerEndpoint $organizationPartners
 * @property PaymentEndpointCollection $payments
 * @property PaymentCaptureEndpoint $paymentCaptures
 * @property PaymentChargebackEndpoint $paymentChargebacks
 * @property PaymentLinkEndpoint $paymentLinks
 * @property PaymentLinkPaymentEndpoint $paymentLinkPayments
 * @property PaymentRefundEndpointCollection $paymentRefunds
 * @property PaymentRouteEndpoint $paymentRoutes
 * @property PermissionEndpoint $permissions
 * @property ProfileEndpoint $profiles
 * @property ProfileMethodEndpoint $profileMethods
 * @property RefundEndpoint $refunds
 * @property SettlementsEndpoint $settlements
 * @property SettlementCaptureEndpoint $settlementCaptures
 * @property SettlementChargebackEndpoint $settlementChargebacks
 * @property SettlementPaymentEndpoint $settlementPayments
 * @property SettlementRefundEndpoint $settlementRefunds
 * @property OrderShipmentEndpoint $shipments
 * @property SubscriptionEndpoint $subscriptions
 * @property SubscriptionPaymentEndpoint $subscriptionPayments
 * @property TerminalEndpoint $terminals
 * @property WalletEndpoint $wallets
 * @property HttpAdapterContract $httpClient
 */
class MollieApiClient implements Connector
{
    use HandlesAuthentication;
    use HandlesAutoHydration;
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
    public const CLIENT_VERSION = '3.0.0';

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
     * @throws \Mollie\Api\Exceptions\IncompatiblePlatform|\Mollie\Api\Exceptions\UnrecognizedClientException
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
        return rtrim($this->apiEndpoint, '/').'/'.self::API_VERSION;
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

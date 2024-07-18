<?php

namespace Mollie\Api;

use Mollie\Api\Contracts\MollieHttpAdapterContract;
use Mollie\Api\Contracts\MollieHttpAdapterPickerContract;
use Mollie\Api\Contracts\ResponseContract as Response;
use Mollie\Api\Endpoints\BalanceEndpoint;
use Mollie\Api\Endpoints\BalanceReportEndpoint;
use Mollie\Api\Endpoints\BalanceTransactionEndpoint;
use Mollie\Api\Endpoints\ChargebackEndpoint;
use Mollie\Api\Endpoints\ClientEndpoint;
use Mollie\Api\Endpoints\ClientLinkEndpoint;
use Mollie\Api\Endpoints\CustomerEndpoint;
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
use Mollie\Api\Endpoints\OrganizationEndpoint;
use Mollie\Api\Endpoints\OrganizationPartnerEndpoint;
use Mollie\Api\Endpoints\PaymentCaptureEndpoint;
use Mollie\Api\Endpoints\PaymentChargebackEndpoint;
use Mollie\Api\Endpoints\PaymentEndpoint;
use Mollie\Api\Endpoints\PaymentLinkEndpoint;
use Mollie\Api\Endpoints\PaymentLinkPaymentEndpoint;
use Mollie\Api\Endpoints\PaymentRefundEndpoint;
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
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\Exceptions\IncompatiblePlatform;
use Mollie\Api\Http\Adapter\MollieHttpAdapterPicker;
use Mollie\Api\Idempotency\IdempotencyKeyGeneratorContract;

/**
 * @property BalanceEndpoint $balances
 * @property BalanceReportEndpoint $balanceReports
 * @property BalanceTransactionEndpoint $balanceTransactions
 * @property ChargebackEndpoint $chargebacks
 * @property ClientEndpoint $clients
 * @property ClientLinkEndpoint $clientLinks
 * @property CustomerPaymentsEndpoint $customerPayments
 * @property CustomerEndpoint $customers
 * @property InvoiceEndpoint $invoices
 * @property MandateEndpoint $mandates
 * @property MethodEndpoint $methods
 * @property MethodIssuerEndpoint $methodIssuers
 * @property OnboardingEndpoint $onboarding
 * @property OrderEndpoint $orders
 * @property OrderLineEndpoint $orderLines
 * @property OrderPaymentEndpoint $orderPayments
 * @property OrderRefundEndpoint $orderRefunds
 * @property OrganizationEndpoint $organizations
 * @property OrganizationPartnerEndpoint $organizationPartners
 * @property PaymentEndpoint $payments
 * @property PaymentCaptureEndpoint $paymentCaptures
 * @property PaymentChargebackEndpoint $paymentChargebacks
 * @property PaymentLinkEndpoint $paymentLinks
 * @property PaymentLinkPaymentEndpoint $paymentLinkPayments
 * @property PaymentRefundEndpoint $paymentRefunds
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
 */
class MollieApiClient
{
    use HandlesIdempotency;
    use HandlesDebugging;

    /**
     * Version of our client.
     */
    public const CLIENT_VERSION = "3.0.0";

    /**
     * Endpoint of the remote API.
     */
    public const API_ENDPOINT = "https://api.mollie.com";

    /**
     * Version of the remote API.
     */
    public const API_VERSION = "v2";

    /**
     * HTTP Methods
     */
    public const HTTP_GET = "GET";
    public const HTTP_POST = "POST";
    public const HTTP_DELETE = "DELETE";
    public const HTTP_PATCH = "PATCH";

    /**
     * @var MollieHttpAdapterContract
     */
    protected MollieHttpAdapterContract $httpClient;

    /**
     * @var string
     */
    protected string $apiEndpoint = self::API_ENDPOINT;

    /**
     * @var string
     */
    protected string $apiKey = '';

    /**
     * True if an OAuth access token is set as API key.
     *
     * @var null|bool
     */
    protected ?bool $oauthAccess = null;

    /**
     * @var array
     */
    protected array $versionStrings = [];

    /**
     * @var array
     */
    protected array $endpoints = [];

    /**
     * @param \GuzzleHttp\ClientInterface|\Mollie\Api\Contracts\MollieHttpAdapterContract|null $client
     * @param MollieHttpAdapterPickerContract|null $adapterPicker,
     * @param IdempotencyKeyGeneratorContract|null $idempotencyKeyGenerator,
     * @throws \Mollie\Api\Exceptions\IncompatiblePlatform|\Mollie\Api\Exceptions\UnrecognizedClientException
     */
    public function __construct(
        $client = null,
        ?MollieHttpAdapterPickerContract $adapterPicker = null,
        ?IdempotencyKeyGeneratorContract $idempotencyKeyGenerator = null
    ) {
        $adapterPicker = $adapterPicker ?: new MollieHttpAdapterPicker;
        $this->httpClient = $adapterPicker->pickHttpAdapter($client);

        $compatibilityChecker = new CompatibilityChecker;
        $compatibilityChecker->checkCompatibility();

        $this->initializeEndpoints();
        $this->initializeVersionStrings();
        $this->initializeIdempotencyKeyGenerator($idempotencyKeyGenerator);
    }

    private function initializeEndpoints(): void
    {
        $endpointClasses = [
            'balances' => BalanceEndpoint::class,
            'balanceReports' => BalanceReportEndpoint::class,
            'balanceTransactions' => BalanceTransactionEndpoint::class,
            'chargebacks' => ChargebackEndpoint::class,
            'clients' => ClientEndpoint::class,
            'clientLinks' => ClientLinkEndpoint::class,
            'customerPayments' => CustomerPaymentsEndpoint::class,
            'customers' => CustomerEndpoint::class,
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
            'organizations' => OrganizationEndpoint::class,
            'paymentCaptures' => PaymentCaptureEndpoint::class,
            'paymentChargebacks' => PaymentChargebackEndpoint::class,
            'paymentLinks' => PaymentLinkEndpoint::class,
            'paymentLinkPayments' => PaymentLinkPaymentEndpoint::class,
            'paymentRefunds' => PaymentRefundEndpoint::class,
            'paymentRoutes' => PaymentRouteEndpoint::class,
            'payments' => PaymentEndpoint::class,
            'permissions' => PermissionEndpoint::class,
            'profiles' => ProfileEndpoint::class,
            'profileMethods' => ProfileMethodEndpoint::class,
            'refunds' => RefundEndpoint::class,
            'settlementCaptures' => SettlementCaptureEndpoint::class,
            'settlementChargebacks' => SettlementChargebackEndpoint::class,
            'settlementPayments' => SettlementPaymentEndpoint::class,
            'settlementRefunds' => SettlementRefundEndpoint::class,
            'settlements' => SettlementsEndpoint::class,
            'shipments' => OrderShipmentEndpoint::class,
            'subscriptions' => SubscriptionEndpoint::class,
            'subscriptionPayments' => SubscriptionPaymentEndpoint::class,
            'terminals' => TerminalEndpoint::class,
            'wallets' => WalletEndpoint::class,
        ];

        foreach ($endpointClasses as $name => $class) {
            $this->endpoints[$name] = $class;
        }
    }

    private function initializeVersionStrings(): void
    {
        $this->addVersionString("Mollie/" . self::CLIENT_VERSION);
        $this->addVersionString("PHP/" . phpversion());

        if ($clientVersion = $this->httpClient->version()) {
            $this->addVersionString($clientVersion);
        }
    }

    /**
     * @param string $url
     *
     * @return self
     */
    public function setApiEndpoint($url): self
    {
        $this->apiEndpoint = rtrim(trim($url), '/');

        return $this;
    }

    /**
     * @return string
     */
    public function getApiEndpoint(): string
    {
        return $this->apiEndpoint;
    }

    /**
     * @return array
     */
    public function getVersionStrings(): array
    {
        return $this->versionStrings;
    }

    /**
     * @param string $apiKey The Mollie API key, starting with 'test_' or 'live_'
     *
     * @return MollieApiClient
     * @throws ApiException
     */
    public function setApiKey(string $apiKey): self
    {
        $apiKey = trim($apiKey);

        if (!preg_match('/^(live|test)_\w{30,}$/', $apiKey)) {
            throw new ApiException("Invalid API key: '{$apiKey}'. An API key must start with 'test_' or 'live_' and must be at least 30 characters long.");
        }

        $this->apiKey = $apiKey;
        $this->oauthAccess = false;

        return $this;
    }

    /**
     * @param string $accessToken OAuth access token, starting with 'access_'
     *
     * @return MollieApiClient
     * @throws ApiException
     */
    public function setAccessToken(string $accessToken): self
    {
        $accessToken = trim($accessToken);

        if (!preg_match('/^access_\w+$/', $accessToken)) {
            throw new ApiException("Invalid OAuth access token: '{$accessToken}'. An access token must start with 'access_'.");
        }

        $this->apiKey = $accessToken;
        $this->oauthAccess = true;

        return $this;
    }

    /**
     * Returns null if no API key has been set yet.
     *
     * @return bool|null
     */
    public function usesOAuth(): ?bool
    {
        return $this->oauthAccess;
    }

    /**
     * @param string $versionString
     *
     * @return MollieApiClient
     */
    public function addVersionString($versionString): self
    {
        $this->versionStrings[] = str_replace([" ", "\t", "\n", "\r"], '-', $versionString);

        return $this;
    }

    /**
     * Perform an HTTP call. This method is used by the resource-specific classes.
     *
     * @param string $method
     * @param string $path
     * @param string|null $body
     *
     * @return Response
     * @throws ApiException
     */
    public function performHttpCall(string $method, string $path, ?string $body = null): Response
    {
        $url = $this->buildApiUrl($path);

        return $this->performHttpCallToFullUrl($method, $url, $body);
    }

    /**
     * Perform an HTTP call to a full URL. This method is used by the resource-specific classes.
     *
     * @param string $method
     * @param string $url
     * @param string|null $body
     *
     * @return Response
     * @throws ApiException
     */
    public function performHttpCallToFullUrl(string $method, string $url, ?string $body = null): Response
    {
        $this->ensureApiKeyIsSet();

        $headers = $this->prepareHeaders($method, $body);
        $response = $this->httpClient->send($method, $url, $headers, $body);

        $this->resetIdempotencyKey();

        return $response;
    }

    /**
     * Build the full API URL for a given method.
     *
     * @param string $path
     * @return string
     */
    private function buildApiUrl(string $path): string
    {
        return rtrim($this->apiEndpoint, '/') . '/' . self::API_VERSION . '/' . ltrim($path, '/');
    }

    /**
     * Ensure that the API key is set.
     *
     * @throws ApiException
     */
    protected function ensureApiKeyIsSet(): void
    {
        if (empty($this->apiKey)) {
            throw new ApiException("You have not set an API key or OAuth access token. Please use setApiKey() to set the API key.");
        }
    }

    /**
     * Prepare the headers for the HTTP request.
     *
     * @param string $method
     * @param string|null $body
     * @return array
     */
    protected function prepareHeaders(string $method, ?string $body): array
    {
        $userAgent = implode(' ', $this->versionStrings)
            . ($this->usesOAuth() ? " OAuth/2.0" : "");

        $headers = [
            'Accept' => "application/json",
            'Authorization' => "Bearer {$this->apiKey}",
            'User-Agent' => $userAgent,
        ];

        if ($body !== null) {
            $headers['Content-Type'] = "application/json";
        }

        if (function_exists("php_uname")) {
            $headers['X-Mollie-Client-Info'] = php_uname();
        }

        return $this->applyIdempotencyKey($headers, $method);
    }

    /**
     * Magic getter to access the endpoints.
     *
     * @param string $name
     *
     * @return mixed
     * @throws \Exception
     */
    public function __get(string $name)
    {
        if (isset($this->endpoints[$name])) {
            return new $this->endpoints[$name]($this);
        }

        throw new \Exception("Undefined endpoint: $name");
    }

    /**
     * @return array
     */
    public function __serialize(): array
    {
        return ['apiEndpoint' => $this->apiEndpoint];
    }

    /**
     * @param array $data
     * @return void
     * @throws IncompatiblePlatform
     */
    public function __unserialize(array $data): void
    {
        $this->__construct();
        $this->apiEndpoint = $data['apiEndpoint'];
    }
}

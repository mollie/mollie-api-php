<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Utils\Arr;
use Mollie\Api\Utils\Str;
use Mollie\Api\Utils\Utility;

/**
 * Centralized mapping between resource classes and their API type names
 * (singular, plural, and aliases). Also provides reverse lookup by type string.
 */
class ResourceRegistry
{
    const PLURAL_KEY = 'plural';

    const SINGULAR_KEY = 'singular';

    /** @var array<class-string<BaseResource>, array{singular: string, plural: string}> */
    private array $byClass = [];

    /** @var array<string, class-string<BaseResource>> */
    private array $byType = [];

    /**
     * Default singleton instance for lookups where dependency injection is not wired.
     */
    private static ?self $default = null;

    /**
     * Optionally allow pre-registration of classes, though defaults cover common resources.
     *
     * @param array<class-string<BaseResource>, string> $resources
     */
    public function __construct(array $resources = [])
    {
        $this->setup($resources);
    }

    /**
     * Get a default singleton instance preloaded with known resources.
     */
    public static function default(): self
    {
        if (! self::$default) {
            self::$default = new self();
        }

        return self::$default;
    }

    /**
     * Resolve a resource class for the given type string (singular/plural/alias).
     */
    public function for(string $type): ?string
    {
        $key = Str::kebab($type);

        return $this->byType[$key] ?? null;
    }

    /**
     * Register or override the mapping for a resource class.
     */
    public function register(string $resourceClass, string $plural, ?string $singular = null): void
    {
        $names = $this->deriveNames($resourceClass, $plural, $singular);

        $this->byClass[$resourceClass] = $names;
        $this->byType[$names[self::SINGULAR_KEY]] = $resourceClass;
        $this->byType[$names[self::PLURAL_KEY]] = $resourceClass;
    }

    /**
     * Get the names for a class (singular, plural, aliases).
     * @return array{singular: string, plural: string}|null
     */
    public function namesOf(string $resourceClass): ?array
    {
        return Arr::get($this->byClass, $resourceClass);
    }

    public function singularOf(string $resourceClass): string
    {
        if (! $this->isRegistered($resourceClass)) {
            $this->throwInvalidArgumentException($resourceClass);
        }

        return Arr::get($this->byClass, $resourceClass . '.' . self::SINGULAR_KEY);
    }

    /**
     * Get the plural (kebab-case) for a resource class.
     */
    public function pluralOf(string $resourceClass): string
    {
        if (! $this->isRegistered($resourceClass)) {
            $this->throwInvalidArgumentException($resourceClass);
        }

        return Arr::get($this->byClass, $resourceClass . '.' . self::PLURAL_KEY);
    }

    public function isRegistered(string $resourceClass): bool
    {
        return Arr::exists($this->byClass, $resourceClass);
    }

    private function setup(array $resources): void
    {
        $resources = array_merge($this->defaultResources(), $resources);

        foreach ($resources as $resourceClass => $plural) {
            $this->register($resourceClass, $plural);
        }
    }

    /**
     * Explicit collection keys for resources that either do not have a collection
     * class or where we want to avoid runtime collection key logic.
     * Values are canonical kebab-case.
     *
     * @return array<class-string<BaseResource>, string>
     */
    private function defaultResources(): array
    {
        return [
            Balance::class => 'balances',
            BalanceReport::class => 'balance-reports',
            BalanceTransaction::class => 'balance-transactions',
            Capability::class => 'capabilities',
            Capture::class => 'captures',
            Chargeback::class => 'chargebacks',
            Client::class => 'clients',
            ClientLink::class => 'client-links',
            ConnectBalanceTransfer::class => 'connect-balance-transfers',
            Customer::class => 'customers',
            Invoice::class => 'invoices',
            Issuer::class => 'issuers',
            Mandate::class => 'mandates',
            Method::class => 'methods',
            MethodPrice::class => 'method-prices',
            Onboarding::class => 'onboardings',
            Organization::class => 'organizations',
            Partner::class => 'partners',
            Payment::class => 'payments',
            PaymentLink::class => 'payment-links',
            Permission::class => 'permissions',
            Profile::class => 'profiles',
            Refund::class => 'refunds',
            Route::class => 'routes',
            SalesInvoice::class => 'sales-invoices',
            Session::class => 'sessions',
            Settlement::class => 'settlements',
            Subscription::class => 'subscriptions',
            Terminal::class => 'terminals',
            Webhook::class => 'webhooks',
            WebhookEvent::class => 'webhook-events',
        ];
    }

    /**
     * Build derived names and alias list for a resource class.
     *
     * @return array{singular: string, plural: string}
     */
    private function deriveNames(string $resourceClass, string $plural, ?string $singular): array
    {
        $singular = Str::kebab($singular ?? Utility::classBasename($resourceClass));

        if (empty($plural)) {
            $this->throwInvalidArgumentException($resourceClass);
        }

        $plural = Str::kebab($plural);

        return [
            self::SINGULAR_KEY => $singular,
            self::PLURAL_KEY => $plural,
        ];
    }

    private function throwInvalidArgumentException(string $resourceClass): void
    {
        throw new \InvalidArgumentException($resourceClass . ' is not registered');
    }
}

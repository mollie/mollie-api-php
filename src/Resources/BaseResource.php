<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Contracts\Connector;
use Mollie\Api\Contracts\IsResponseAware;
use Mollie\Api\Traits\HasResponse;

/**
 * Base class for all API resource models.
 *
 * Resources use a **two-tier** hydration model to stay forward-compatible with
 * Mollie API evolution without requiring an SDK release for every new field:
 *
 *  - **Tier 1 — declared typed properties:** Subclasses declare properties with
 *    real PHP types (scalars, backed enums, value objects from
 *    {@see \Mollie\Api\Http\Data}, nullable combinations). The
 *    {@see ResourceHydrator} reflects these declarations once per class
 *    (cached) and coerces the JSON-decoded value into the declared type —
 *    turning e.g. an `amount` object into a {@see \Mollie\Api\Http\Data\Money}
 *    instance, a `status` string into a backed enum case, etc.
 *  - **Tier 2 — undeclared fields:** Any JSON key the SDK doesn't yet know
 *    about is assigned as a PHP 8.2 dynamic property (stdClass / scalar /
 *    array), matching v3 behavior. The `#[\AllowDynamicProperties]` attribute
 *    below keeps this path deprecation-free on PHP 8.2+.
 *
 * `_links` and `_embedded` always live on tier 2 — they are structural HAL
 * metadata, not domain objects.
 */
#[\AllowDynamicProperties]
abstract class BaseResource implements IsResponseAware
{
    use HasResponse;

    protected Connector $connector;

    /**
     * Indicates the type of resource.
     *
     * @var string
     */
    public $resource;

    public function __construct(Connector $connector)
    {
        $this->connector = $connector;
    }

    public function getConnector(): Connector
    {
        return $this->connector;
    }
}

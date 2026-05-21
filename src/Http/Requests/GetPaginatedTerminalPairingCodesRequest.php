<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Resources\TerminalPairingCodeCollection;
use Mollie\Api\Traits\IsIteratableRequest;
use Mollie\Api\Types\Method;

/**
 * @see https://docs.mollie.com/reference/terminals-list-pairing-codes
 *
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\TerminalPairingCodeCollection>
 */
class GetPaginatedTerminalPairingCodesRequest extends ResourceHydratableRequest implements IsIteratable
{
    use IsIteratableRequest;

    protected static string $method = Method::GET;

    /**
     * The resource class the request should be casted to.
     */
    protected ?string $hydratableResource = TerminalPairingCodeCollection::class;

    public function __construct(
        private ?string $from = null,
        private ?int $limit = null,
        private ?string $sort = null,
        private ?string $profileId = null,
    ) {
    }

    protected function defaultQuery(): array
    {
        return [
            'from' => $this->from,
            'limit' => $this->limit,
            'sort' => $this->sort,
            'profileId' => $this->profileId,
        ];
    }

    public function resolveResourcePath(): string
    {
        return 'terminals/pairing-codes';
    }
}

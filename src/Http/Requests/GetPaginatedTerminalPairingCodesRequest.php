<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Resources\TerminalPairingCodeCollection;
use Mollie\Api\Traits\IsIteratableRequest;

/**
 * @see https://docs.mollie.com/reference/terminals-list-pairing-codes
 */
class GetPaginatedTerminalPairingCodesRequest extends SortablePaginatedRequest implements IsIteratable
{
    use IsIteratableRequest;

    protected $hydratableResource = TerminalPairingCodeCollection::class;

    private ?string $profileId;

    public function __construct(
        ?string $from = null,
        ?int $limit = null,
        ?string $profileId = null,
        ?string $sort = null
    ) {
        parent::__construct($from, $limit, $sort);

        $this->profileId = $profileId;

        $this->query()->add('profileId', $this->profileId);
    }

    public function resolveResourcePath(): string
    {
        return 'terminals/pairing-codes';
    }
}

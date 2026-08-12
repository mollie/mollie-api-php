<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Resources\Session;
use Mollie\Api\Types\Method;

/**
 * @extends ResourceHydratableRequest<\Mollie\Api\Resources\Session>
 */
class GetSessionRequest extends ResourceHydratableRequest
{
    protected static string $method = Method::GET;

    protected ?string $hydratableResource = Session::class;

    public function __construct(
        private string $id,
    ) {
    }

    public function resolveResourcePath(): string
    {
        return 'sessions/'.$this->id;
    }
}

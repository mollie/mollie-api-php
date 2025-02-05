<?php

namespace Mollie\Api\Http\Requests;

use Mollie\Api\Contracts\IsIteratable;
use Mollie\Api\Traits\IsIteratableRequest;

class DynamicPaginatedRequest extends DynamicGetRequest implements IsIteratable
{
    use IsIteratableRequest;
}

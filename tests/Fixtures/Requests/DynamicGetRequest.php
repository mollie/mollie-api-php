<?php

declare(strict_types=1);

namespace Tests\Fixtures\Requests;

use Mollie\Api\Contracts\SupportsTestmodeInQuery;
use Mollie\Api\Http\Requests\DynamicGetRequest as BaseDynamicGetRequest;

class DynamicGetRequest extends BaseDynamicGetRequest implements SupportsTestmodeInQuery
{
}

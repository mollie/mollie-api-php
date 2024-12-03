<?php

namespace Tests\Fixtures\Requests;

use Mollie\Api\Http\Requests\DynamicGetRequest as BaseDynamicGetRequest;
use Mollie\Api\Contracts\SupportsTestmodeInQuery;

class DynamicGetRequest extends BaseDynamicGetRequest implements SupportsTestmodeInQuery
{
}

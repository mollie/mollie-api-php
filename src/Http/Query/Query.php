<?php

namespace Mollie\Api\Http\Query;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Contracts\ValidatableDataProvider;
use Mollie\Api\Traits\HasRules;

abstract class Query implements Arrayable, ValidatableDataProvider
{
    use HasRules;
}

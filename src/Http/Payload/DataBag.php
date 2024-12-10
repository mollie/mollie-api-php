<?php

namespace Mollie\Api\Http\Payload;

use Mollie\Api\Contracts\Arrayable;
use Mollie\Api\Contracts\DataResolver;
use Mollie\Api\Traits\ResolvesValues;

abstract class DataBag implements Arrayable, DataResolver
{
    use ResolvesValues;
}

<?php

namespace Mollie\Api\Exceptions;

use Psr\Http\Client\ClientExceptionInterface;

abstract class MollieException extends \Exception implements ClientExceptionInterface
{
}

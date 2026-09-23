<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

enum OrderLineUpdateOperationType: string
{
    case Add = 'add';
    case Cancel = 'cancel';
    case Update = 'update';
}

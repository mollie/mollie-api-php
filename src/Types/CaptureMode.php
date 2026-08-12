<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

enum CaptureMode: string
{
    case Manual = 'manual';
    case Automatic = 'automatic';
}

<?php

declare(strict_types=1);

namespace Mollie\Api\Types;

enum SessionStatus: string
{
    case Created = 'created';
    case ReadyForProcessing = 'ready_for_processing';
    case Completed = 'completed';
    case Failed = 'failed';
}

<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

use Mollie\Api\Types\MandateStatus;
use Mollie\Api\Utils\Utility;

/**
 * @extends CursorCollection<\Mollie\Api\Resources\Mandate>
 */
class MandateCollection extends CursorCollection
{
    /**
     * The name of the collection resource in Mollie's API.
     */
    public static string $collectionName = 'mandates';

    /**
     * Resource class name.
     */
    public static string $resource = Mandate::class;

    public function whereStatus(MandateStatus|string $status): self
    {
        return $this->filter(fn (Mandate $mandate) => Utility::equals($mandate->status, $status));
    }
}

<?php

declare(strict_types=1);

namespace Mollie\Api\Resources;

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

    /**
     * @param  string  $status
     */
    public function whereStatus($status): self
    {
        return $this->filter(fn (Mandate $mandate) => $mandate->status === $status);
    }
}

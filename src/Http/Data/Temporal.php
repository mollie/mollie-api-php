<?php

declare(strict_types=1);

namespace Mollie\Api\Http\Data;

use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use Mollie\Api\Contracts\Stringable;

abstract readonly class Temporal implements Stringable
{
    protected DateTimeInterface $date;

    public function __construct(DateTimeInterface|string $date)
    {
        if (! $date instanceof DateTimeInterface) {
            try {
                $date = new DateTimeImmutable($date);
            } catch (\Exception $e) {
                throw new InvalidArgumentException('Invalid date format', 0, $e);
            }
        }

        $this->date = $date;
    }

    abstract protected function getFormat(): string;

    public function __toString(): string
    {
        return $this->date->format($this->getFormat());
    }

    public function getRaw(): DateTimeInterface
    {
        return $this->date;
    }
}

<?php

namespace Mollie\Api\Http\Data;

use DateTimeImmutable;
use DateTimeInterface;
use InvalidArgumentException;
use Mollie\Api\Contracts\Stringable;

abstract class Temporal implements Stringable
{
    protected DateTimeInterface $date;

    /**
     * @param  DateTimeInterface|string  $date
     */
    public function __construct($date)
    {
        if (! $date instanceof DateTimeInterface) {
            $date = DateTimeImmutable::createFromFormat($this->getFormat(), $date);

            $this->guardInvalidDate($date);
        }

        /** @var DateTimeInterface $date */
        $this->date = $date;
    }

    abstract protected function getFormat(): string;

    public function __toString(): string
    {
        return $this->date->format($this->getFormat());
    }

    /**
     * @param  DateTimeInterface|false  $date
     */
    private function guardInvalidDate($date): void
    {
        if ($date === false) {
            throw new InvalidArgumentException('Invalid date format');
        }
    }
}

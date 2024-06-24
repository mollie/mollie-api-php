<?php

namespace Mollie\Api\Types;

class SequenceType
{
    /**
     * Sequence types.
     *
     * @see https://docs.mollie.com/guides/recurring
     */
    public const ONEOFF = "oneoff";
    public const FIRST = "first";
    public const RECURRING = "recurring";
}

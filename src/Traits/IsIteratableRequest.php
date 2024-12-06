<?php

namespace Mollie\Api\Traits;

trait IsIteratableRequest
{
    protected bool $iteratorEnabled = false;

    protected bool $iterateBackwards = false;

    public function iteratorEnabled(): bool
    {
        return $this->iteratorEnabled;
    }

    public function iteratesBackwards(): bool
    {
        return $this->iterateBackwards;
    }

    public function useIterator(): self
    {
        $this->iteratorEnabled = true;

        return $this;
    }

    public function setIterationDirection(bool $iterateBackwards = false): self
    {
        $this->iterateBackwards = $iterateBackwards;

        return $this;
    }
}

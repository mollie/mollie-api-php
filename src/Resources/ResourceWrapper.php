<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Contracts\IsWrapper;
use Mollie\Api\Traits\ForwardsCalls;

abstract class ResourceWrapper implements IsWrapper
{
    use ForwardsCalls;

    protected $wrapped;

    public function setWrapped($wrapped): self
    {
        $this->wrapped = $wrapped;

        return $this;
    }

    public function wrap($resource): self
    {
        return $this->setWrapped($resource);
    }

    /**
     * @return mixed
     */
    public function getWrapped()
    {
        return $this->wrapped;
    }

    public function __get($name)
    {
        return $this->wrapped->{$name};
    }

    public function __call($name, $arguments)
    {
        return $this->forwardDecoratedCallTo($this->wrapped, $name, $arguments);
    }
}

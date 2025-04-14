<?php

namespace Mollie\Api\Resources;

use Mollie\Api\Contracts\IsWrapper;
use Mollie\Api\Traits\ForwardsCalls;

abstract class ResourceWrapper implements IsWrapper
{
    use ForwardsCalls;

    protected $wrapped;

    /**
     * @param  mixed  $wrapped
     * @return static
     */
    public function setWrapped($wrapped)
    {
        $this->wrapped = $wrapped;

        return $this;
    }

    /**
     * @param  mixed  $resource
     * @return static
     */
    public function wrap($resource)
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

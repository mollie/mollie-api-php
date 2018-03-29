<?php

namespace Mollie\Api\Exceptions;

class ApiException extends \Exception
{
    /**
     * @var string
     */
    protected $field;

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param string $field
     */
    public function setField($field)
    {
        $this->field = (string)$field;
    }
}

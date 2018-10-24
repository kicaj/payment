<?php
namespace Payment\Exception;

use Cake\Core\Exception\Exception;

class InterfaceException extends Exception
{

    /**
     * {@inheritDoc}
     */
    public function __construct($message, $code = 500, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

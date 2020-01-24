<?php
namespace Payment\Exception;

use Cake\Http\Exception\UnauthorizedException;

class StatusUnauthorizedException extends UnauthorizedException
{

    /**
     * {@inheritDoc}
     */
    public function __construct($message = 'Unauthorized status request.', $code = null, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

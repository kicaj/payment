<?php
namespace Payment\Exception;

use Cake\Http\Exception\BadRequestException;

class RedirectParamException extends BadRequestException
{

    /**
     * {@inheritDoc}
     */
    public function __construct($message = 'Missing param.', $code = null, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

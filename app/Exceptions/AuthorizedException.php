<?php

namespace App\Exceptions;

class AuthorizedException extends BaseException
{
    public function __construct(string $message, int $code)
    {
        parent::__construct($message, $code);
    }
}

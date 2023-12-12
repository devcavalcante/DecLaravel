<?php

namespace App\Exceptions;

class EmailExists extends BaseException
{
    public function __construct()
    {
        parent::__construct('Esse e-mail ja esta cadastrado');
    }
}

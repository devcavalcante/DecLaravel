<?php

namespace App\Exceptions;

class OnlyRepresentativesException extends BaseException
{
    public function __construct()
    {
        parent::__construct('Usuários do tipo gerente não podem ser representantes');
    }
}

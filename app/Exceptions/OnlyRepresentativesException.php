<?php

namespace App\Exceptions;

class OnlyRepresentativesException extends BaseException
{
    public function __construct()
    {
        parent::__construct('Apenas usuários do tipo representante são permitidos');
    }
}

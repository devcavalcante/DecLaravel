<?php

namespace App\Exceptions;

class OnlyRepresentativesException extends BaseException
{
    public function __construct()
    {
        parent::__construct('Apenas usuarios do tipo representante sao permitidos');
    }
}

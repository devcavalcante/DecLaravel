<?php

namespace App\Exceptions;

class MembersExists extends BaseException
{
    public function __construct()
    {
        parent::__construct('Membro ja cadastrado no grupo');
    }
}

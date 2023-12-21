<?php

namespace App\Enums;

enum TypeUserEnum
{
    public const VIEWER = 'visualizador';
    public const ADMIN = 'administrador';
    public const MANAGER = 'gerente';
    public const REPRESENTATIVE = 'representante';
    public const MEMBER = 'membro';
}

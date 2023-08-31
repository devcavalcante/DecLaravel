<?php

namespace App\Helpers;

use App\Enums\TypeUserEnum;

class TypeUser
{
    public static function listOfKeysTypeUserEnum(): array
    {
        $typeUsers = [
            TypeUserEnum::MANAGER,
            TypeUserEnum::VIEWER,
            TypeUserEnum::REPRESENTATIVE,
            TypeUserEnum::ADMIN,
        ];

        return array_keys($typeUsers);
    }
}

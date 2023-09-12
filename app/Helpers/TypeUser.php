<?php

namespace App\Helpers;

use App\Enums\TypeUserEnum;

class TypeUser
{
    public static function listOfKeysTypeUserEnum(): array
    {
        $typeUsers = [
            1 => TypeUserEnum::MANAGER,
            2 => TypeUserEnum::VIEWER,
            3 => TypeUserEnum::REPRESENTATIVE,
            4 => TypeUserEnum::ADMIN,
        ];

        return array_keys($typeUsers);
    }
}

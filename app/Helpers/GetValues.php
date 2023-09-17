<?php

namespace App\Helpers;

use App\Enums\TypeGroupEnum;
use App\Enums\TypeUserEnum;

class GetValues
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
    public static function listOfValuesTypeGroupEnum(): array
    {
        $typeGroups = [
            TypeGroupEnum::INTERNO,
            TypeGroupEnum::EXTERNO,
        ];

        return array_values($typeGroups);
    }
}

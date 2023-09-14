<?php

namespace App\Helpers;

use App\Enums\TypeGroupEnum;
use App\Enums\TypeUserEnum;

class GetKeys
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
    public static function listOfKeysTypeGroupEnum(): array
    {
        $typeGroups = [
            1 => TypeGroupEnum::INTERNO,
            2 => TypeGroupEnum::EXTERNO,
        ];

        return array_keys($typeGroups);
    }
}

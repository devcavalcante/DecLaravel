<?php

namespace Tests\Feature\Utils;

use App\Models\TypeUser;
use App\Models\User;
use Laravel\Passport\Passport;

trait LoginUsersTrait
{
    private function login(string $typeUser): User
    {
        $typeUser = TypeUser::where('name', $typeUser)->first();
        $user = User::where('type_user_id', $typeUser->id)->first();
        Passport::actingAs($user);
        return $user;
    }
}

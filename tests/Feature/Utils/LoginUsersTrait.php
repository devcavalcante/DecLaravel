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
        $user = User::factory(['type_user_id' => $typeUser->id])->create();
        Passport::actingAs($user);
        return $user;
    }
}

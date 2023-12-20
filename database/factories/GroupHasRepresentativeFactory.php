<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Representative;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupHasRepresentativeFactory extends Factory
{
    protected $model = Representative::class;

    public function definition(): array
    {
        $group = Group::first();
        $user = User::where(['type_user_id' => 3])->first();

        return [
            'group_id' => $group->id,
            'user_id'  => $user->id,
        ];
    }
}

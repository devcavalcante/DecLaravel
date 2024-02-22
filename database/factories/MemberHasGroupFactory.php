<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Member;
use App\Models\MemberHasGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class MemberHasGroupFactory extends Factory
{
    protected $model = MemberHasGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $member = Member::factory()->create();
        $group = Group::factory()->create();

        return [
            'member_id' => $member->id,
            'group_id'  => $group->id,
        ];
    }
}

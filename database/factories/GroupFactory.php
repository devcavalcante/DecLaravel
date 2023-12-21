<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Representative;
use App\Models\TypeGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GroupFactory extends Factory
{
    protected $model = Group::class;

    public function definition(): array
    {
        $typeGroup = TypeGroup::first();
        $user = User::where(['type_user_id' => 2])->first();
        $representative = Representative::factory()->create();

        return [
            'entity'             => $this->faker->word,
            'organ'              => $this->faker->word,
            'council'            => $this->faker->word,
            'acronym'            => $this->faker->word,
            'team'               => $this->faker->word,
            'unit'               => $this->faker->word,
            'email'              => $this->faker->email,
            'office_requested'   => $this->faker->word,
            'office_indicated'   => $this->faker->word,
            'internal_concierge' => $this->faker->word,
            'observations'       => $this->faker->text,
            'type_group_id'      => $typeGroup->id,
            'creator_user_id'    => $user->id,
            'representative_id'  => $representative->id,
        ];
    }
}

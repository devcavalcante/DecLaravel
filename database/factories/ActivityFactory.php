<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityFactory extends Factory
{
    protected $model = Activity::class;
    public function definition(): array
    {
        $group = Group::factory()->create();

        return [
            'name'        => $this->faker->word,
            'description' => $this->faker->text,
            'group_id'    => $group->id,
        ];
    }
}

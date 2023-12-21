<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\Group;
use Carbon\Carbon;
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
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addWeek(),
            'done_at'   => null,
            'group_id'    => $group->id,
        ];
    }
}

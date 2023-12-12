<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Meeting;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeetingFactory extends Factory
{
    protected $model = Meeting::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $group = Group::factory()->create();

        return [
            'content'   => $this->faker->word,
            'summary'   => $this->faker->text,
            'ata'       => $this->faker->url,
            'date_meet' => $this->faker->date,
            'group_id'    => $group->id,
        ];
    }
}

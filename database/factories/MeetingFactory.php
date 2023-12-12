<?php

namespace Database\Factories;

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
        return [
            'content'   => $this->faker->word,
            'summary'   => $this->faker->text,
            'ata'       => $this->faker->word,
            'date_meet' => $this->faker->date,
        ];
    }
}

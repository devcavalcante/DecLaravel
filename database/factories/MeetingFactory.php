<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MeetingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => $this->faker->sentence(100),
            'summary' => $this->faker->sentence(50),
            'ata'     => $this->faker->paragraph(20),
            'date'    => $this->faker->dateTime(),
        ];
    }
}

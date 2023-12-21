<?php

namespace Database\Factories;

use App\Models\TypeUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class RepresentativeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory()->create();

        return [
            'email'   => $this->faker->email(),
            'user_id' => $user->id, // Associe o ID do usuário ao campo user_id
        ];
    }
}

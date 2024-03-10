<?php

namespace Database\Factories;

use App\Models\Representative;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class RepresentativeFactory extends Factory
{
    protected $model = Representative::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory()->create();

        return [
            'name'    => $this->faker->text,
            'email'   => $this->faker->email(),
            'user_id' => $user->id, // Associe o ID do usuário ao campo user_id
        ];
    }
}

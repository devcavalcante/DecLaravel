<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class MemberFactory extends Factory
{
    protected $model = Member::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Recupera um usuário aleatório para associar ao membro
        $user = User::factory()->create();

        return [
            'name'           => $this->faker->text,
            'email'          => $this->faker->email,
            'role'           => $this->faker->word(),
            'phone'          => $this->faker->phoneNumber(),
            'entry_date'     => now(),
            'departure_date' => $this->faker->dateTimeBetween(now(), '+1 year'),
            'user_id'        => $user->id, // Associe o ID do usuário ao campo user_id
        ];
    }
}

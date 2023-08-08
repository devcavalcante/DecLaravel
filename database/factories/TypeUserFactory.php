<?php

namespace Database\Factories;

use App\Models\TypeUser;
use Illuminate\Database\Eloquent\Factories\Factory;

class TypeUserFactory extends Factory
{
    protected $model = TypeUser::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
        ];
    }
}

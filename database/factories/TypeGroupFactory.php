<?php

namespace Database\Factories;

use App\Models\TypeGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class TypeGroupFactory extends Factory
{
    protected $model = TypeGroup::class;

    public function definition(): array
    {
        return [
            'name'       => $this->faker->word,
            'type_group' => $this->faker->word,
        ];
    }
}

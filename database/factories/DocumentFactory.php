<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\Group;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    protected $model = Document::class;
    public function definition(): array
    {
        $group = Group::factory()->create();

        return [
            'name'      => $this->faker->word,
            'file'      => $this->faker->imageUrl,
            'file_size' => $this->faker->numerify,
            'group_id'  => $group->id,
        ];
    }
}

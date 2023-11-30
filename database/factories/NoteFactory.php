<?php

namespace Database\Factories;

use App\Enums\ColorsEnum;
use App\Models\Group;
use App\Models\Note;
use Illuminate\Database\Eloquent\Factories\Factory;

class NoteFactory extends Factory
{
    protected $model = Note::class;
    public function definition(): array
    {
        $group = Group::factory()->create();

        return [
            'title'       => $this->faker->word,
            'description' => $this->faker->text,
            'color'       => ColorsEnum::YELLOW,
            'group_id'    => $group->id,
        ];
    }
}

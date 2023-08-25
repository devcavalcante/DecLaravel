<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Factories\TypeUserFactory;
use App\Models\TypeUser;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(TypeUserSeeder::class);

        $this->call(TypeUserSeeder::class);
        TypeUser::factory()->count(10)->create();

        $this->call([UserSeeder::class]);
    }
}

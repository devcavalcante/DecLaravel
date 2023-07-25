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
<<<<<<< HEAD

        $this->call(TypeUserSeeder::class);
        TypeUser::factory()->count(10)->create();
=======
        $this->call(TypeUserSeeder::class);
>>>>>>> origin/DEC-45-backend-crud-tipos-de-usuarios
    }
}

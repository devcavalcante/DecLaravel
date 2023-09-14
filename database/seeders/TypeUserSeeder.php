<?php

namespace Database\Seeders;

use App\Enums\TypeUserEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            TypeUserEnum::ADMIN,
            TypeUserEnum::MANAGER,
            TypeUserEnum::REPRESENTATIVE,
            TypeUserEnum::VIEWER,
        ];

        foreach ($types as $type) {
            DB::table('type_users')->insert([
                'name'       => $type,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);
        }
    }
}

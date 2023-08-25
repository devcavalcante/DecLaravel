<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run()
    {
        $typeUserIds = [1, 2, 3, 4]; // IDs válidos de type_users

        for ($i = 1; $i <= 10; $i++) {
            DB::table('users')->insert([
                'name'              => Str::random(10),
                'email'             => Str::random(10) . '@example.com',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
                'type_user_id'      => $typeUserIds[array_rand($typeUserIds)],
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }
}

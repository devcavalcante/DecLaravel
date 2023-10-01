<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::Table('users')->insertOrIgnore(
            [
                [
                    'name'         => 'Debora Cavalcante',
                    'email'        => 'debs@mail.com',
                    'password'     => Hash::make('visualizador'),
                    'type_user_id' => 4,
                    'email_verified_at' => Carbon::now(),
                    'created_at'   => Carbon::now(),
                    'updated_at'   => Carbon::now(),
                ],
                [
                    'name'         => 'Administrador',
                    'email'        => 'admin@mail.com',
                    'password'     => Hash::make('administrador'),
                    'type_user_id' => 1,
                    'email_verified_at' => Carbon::now(),
                    'created_at'   => Carbon::now(),
                    'updated_at'   => Carbon::now(),
                ],
                [
                    'name'         => 'Gerente',
                    'email'        => 'gerente@mail.com',
                    'password'     => Hash::make('gerente'),
                    'type_user_id' => 2,
                    'email_verified_at' => Carbon::now(),
                    'created_at'   => Carbon::now(),
                    'updated_at'   => Carbon::now(),
                ],
                [
                    'name'         => 'Representante',
                    'email'        => 'representante@mail.com',
                    'password'     => Hash::make('representante'),
                    'type_user_id' => 3,
                    'email_verified_at' => Carbon::now(),
                    'created_at'   => Carbon::now(),
                    'updated_at'   => Carbon::now(),
                ],
            ]
        );
    }
}

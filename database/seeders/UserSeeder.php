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
                    'active'       => true,
                    'url_photo'    => 'http://foto.com',
                    'created_at'   => Carbon::now(),
                    'updated_at'   => Carbon::now(),
                ],
                [
                    'name'         => 'Administrador',
                    'email'        => 'admin@mail.com',
                    'password'     => Hash::make('administrador'),
                    'active'       => true,
                    'url_photo'    => 'http://foto.com',
                    'type_user_id' => 1,
                    'created_at'   => Carbon::now(),
                    'updated_at'   => Carbon::now(),
                ],
                [
                    'name'         => 'Gerente',
                    'email'        => 'gerente@mail.com',
                    'password'     => Hash::make('gerente'),
                    'active'       => true,
                    'url_photo'    => 'http://foto.com',
                    'type_user_id' => 2,
                    'created_at'   => Carbon::now(),
                    'updated_at'   => Carbon::now(),
                ],
                [
                    'name'         => 'Representante',
                    'email'        => 'representante@mail.com',
                    'password'     => Hash::make('representante'),
                    'active'       => true,
                    'url_photo'    => 'http://foto.com',
                    'type_user_id' => 3,
                    'created_at'   => Carbon::now(),
                    'updated_at'   => Carbon::now(),
                ],
                [
                    'name'         => 'Representante2',
                    'email'        => 'representant2e@mail.com',
                    'password'     => Hash::make('representante'),
                    'active'       => true,
                    'url_photo'    => 'http://foto.com',
                    'type_user_id' => 3,
                    'created_at'   => Carbon::now(),
                    'updated_at'   => Carbon::now(),
                ],
                [
                    'name'         => 'John Doe',
                    'email'        => 'john.doe@example.com',
                    'password'     => null,
                    'active'       => true,
                    'url_photo'    => 'https://example.com/john_photo.jpg',
                    'type_user_id' => 1,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ],
                [
                    'name'         => 'Jane Smith',
                    'email'        => 'jane.smith@example.com',
                    'password'     => null,
                    'active'       => true,
                    'url_photo'    => 'https://example.com/jane_photo.jpg',
                    'type_user_id' => 1,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ],
                [
                    'name'         => 'Bob Johnson',
                    'email'        => 'bob.johnson@example.com',
                    'password'     => null,
                    'active'       => true,
                    'url_photo'    => 'https://example.com/bob_photo.jpg',
                    'type_user_id' => 1,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ],
                [
                    'name'         => 'Alice Williams',
                    'email'        => 'alice.williams@example.com',
                    'password'     => null,
                    'active'       => true,
                    'url_photo'    => 'https://example.com/alice_photo.jpg',
                    'type_user_id' => 1,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ],
                [
                    'name'         => 'Charlie Brown',
                    'email'        => 'charlie.brown@example.com',
                    'password'     => null,
                    'active'       => true,
                    'url_photo'    => 'https://example.com/charlie_photo.jpg',
                    'type_user_id' => 1,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ],
            ]
        );
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ApiTokenSeeder extends Seeder
{
    public function run(): void
    {
        DB::Table('api_tokens')->insertOrIgnore([
            [
                'user_id'              => 6,
                'api_token'            => '8eb8f238-6067-4f92-a481-a7adfe7a6563',
                'api_token_expires_at' => '2023-12-31 00:00:00',
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
            [
                'user_id'              => 7,
                'api_token'            => '1a2b3c4d-5e6f-7g8h-9i10j11k12l',
                'api_token_expires_at' => '2023-12-31 00:00:00',
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
            [
                'user_id'              => 8,
                'api_token'            => 'abcdef12-3456-7890-1234-567890abcdef',
                'api_token_expires_at' => '2023-12-31 00:00:00',
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
            [
                'user_id'              => 9,
                'api_token'            => '98765432-10ab-cdef-0123-4567890abcde',
                'api_token_expires_at' => '2023-12-31 00:00:00',
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
            [
                'user_id'              => 10,
                'api_token'            => 'aaaa1111-bbbb-cccc-dddd-eeeeeeffffff',
                'api_token_expires_at' => '2023-12-31 00:00:00',
                'created_at'           => now(),
                'updated_at'           => now(),
            ],
        ]);
    }
}

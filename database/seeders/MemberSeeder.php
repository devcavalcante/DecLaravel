<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('members')->insertOrIgnore([
            [
                'role'           => 'Professor',
                'phone'          => '1234567890',
                'departure_date' => now(),
                'user_id'        => 1,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ],
            [
                'role'           => 'Professor',
                'phone'          => '9876543210',
                'departure_date' => now(),
                'user_id'        => 2,
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ],
        ]);
    }
}

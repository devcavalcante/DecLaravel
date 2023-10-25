<?php

namespace Database\Seeders;

use App\Models\Group;
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
        $group = Group::factory()->create();
        DB::table('members')->insertOrIgnore([
            [
                'role'           => 'Professor',
                'phone'          => '1234567890',
                'entry_date'     => Carbon::now(),
                'departure_date' => Carbon::now(),
                'user_id'        => 1,
                'group_id'       => $group->id,
            ],
            [
                'role'           => 'Professor',
                'phone'          => '9876543210',
                'entry_date'     => Carbon::now(),
                'departure_date' => Carbon::now(),
                'user_id'        => 2,
                'group_id'       => $group->id,
            ],
        ]);
    }
}

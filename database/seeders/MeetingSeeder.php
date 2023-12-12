<?php

namespace Database\Seeders;

use App\Models\Group;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MeetingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $group = Group::factory()->create();
        DB::table('meetings')->insertOrIgnore([
            [
                'content'   => 'Reunião inicial',
                'summary'   => 'discuções da reunião',
                'ata'       => 'ata e afins',
                'date_meet' => Carbon::now(),
                'groups_id' => $group->id,
            ],
            [
                'content'   => 'Reunião final',
                'summary'   => 'discuções da reunião',
                'ata'       => 'ata e afins',
                'date_meet' => Carbon::now(),
                'groups_id' => $group->id,
            ],
        ]);
    }
}

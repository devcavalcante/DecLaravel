<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class TypeGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::Table('type_groups')->insertOrIgnore(
            [
                [
                    'name'         => 'Comite',
                    'type_group'        => 'interno',
                    'created_at'   => Carbon::now(),
                    'updated_at'   => Carbon::now(),
                ],
                [
                    'name'         => 'Comissão',
                    'type_group'        => 'externo',
                    'created_at'   => Carbon::now(),
                    'updated_at'   => Carbon::now(),
                ]
            ]
        );
    }
}

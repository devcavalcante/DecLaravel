<?php

namespace Database\Seeders;


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
            'Administrador',
            'Gerente',
            'Representante',
            'Visualizador',
        ];

        foreach ($types as $type) {
            DB::table('type_users')->insert([
                'name' => $type,
                'created_at' => date("Y-m-d H:i:s"),
            ]);
        }
    }
}

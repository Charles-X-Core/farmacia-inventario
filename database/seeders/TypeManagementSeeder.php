<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeManagementSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('type_management')->insert([
            ['id' => 1, 'name' => 'LIBRE', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'name' => 'LOTES', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class typeDocumentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         DB::table('type_documents')->insert([
        ['name' => 'DNI'],
        ['name' => 'RUC'],
        ['name' => 'PASAPORTE'],
        ['name' => 'CARNÉ DE EXTRANJERIA'],
        ]);
    }
}

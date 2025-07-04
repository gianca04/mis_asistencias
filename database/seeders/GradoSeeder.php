<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("Grados")->insert([
                ['nombre' => 'Primero'],
                ['nombre' => 'Segundo'],
                ['nombre' => 'Tercero'],
                ['nombre' => 'Cuarto'],
                ['nombre' => 'Quinto'],
                ['nombre' => 'Sexto']
            ]);
    }
}

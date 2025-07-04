<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeccionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table("Secciones")->insert([
            ['nombre' => 'A'],
            ['nombre' => 'B'],
            ['nombre' => 'C'],
            ['nombre' => 'D'],
            ['nombre' => 'E'],
            ['nombre' => 'F']
        ]);
    }
}

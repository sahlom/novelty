<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = ['Contabilidad', 'Recursos Humanos', 'Recepción', 'Tesorería'];
        foreach ($areas as $area) {
            \App\Models\Area::create(['name' => $area]);
        }
    }
}

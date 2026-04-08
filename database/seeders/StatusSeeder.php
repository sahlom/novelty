<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = ['Pendiente', 'En proceso', 'Completado', 'Detenido'];
        foreach ($statuses as $status) {
            \App\Models\Status::create(['name' => $status]);
        }
    }
}

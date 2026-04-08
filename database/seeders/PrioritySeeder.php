<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $priorities = ['Baja', 'Media', 'Alta', 'Urgente'];
        foreach ($priorities as $priority) {
            \App\Models\Priority::create(['name' => $priority]);
        }
    }
}

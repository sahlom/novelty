<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Benjamín',
            'email' => 'benjamin@sahlom.com',
            'password' => bcrypt('Sahlom*2014.'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'María Guadalupe Patricia Navarro Ibarra',
            'email' => 'direccion@gruponovelty.com',
            'password' => bcrypt('Novelty'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Ivan	Navarro Hernández',
            'email' => 'ivan.navarro@gruponovelty.com',
            'password' => bcrypt('Novelty'),
            'role' => 'usuario',
        ]);

        User::create([
            'name' => 'Gabriela Salomé Sánchez Romero',
            'email' => 'asistente.direccion@gruponovelty.com',
            'password' => bcrypt('Novelty'),
            'role' => 'usuario',
        ]);

        User::create([
            'name' => 'Diana Laura Sánchez Romero',
            'email' => 'recepcion@gruponovelty.com',
            'password' => bcrypt('Novelty'),
            'role' => 'usuario',
        ]);

        User::create([
            'name' => 'José Valentín Cárdenas Pérez',
            'email' => 'auditoria@gruponovelty.com',
            'password' => bcrypt('Novelty'),
            'role' => 'usuario',
        ]);

        User::create([
            'name' => 'Martha Carolina López Arellano',
            'email' => 'contabilidad3@gruponovelty.com',
            'password' => bcrypt('Novelty'),
            'role' => 'usuario',
        ]);

        User::create([
            'name' => 'Janeth Guadalupe Terrones Sánchez',
            'email' => 'contabilidad1@gruponovelty.com',
            'password' => bcrypt('Novelty'),
            'role' => 'usuario',
        ]);

        User::create([
            'name' => 'Gerardo Santos Velarde',
            'email' => 'recursoshumanos@gruponovelty.com',
            'password' => bcrypt('Novelty'),
            'role' => 'usuario',
        ]);

        User::create([
            'name' => 'Valeria Getsemani Gámez González',
            'email' => 'nominas1@gruponovelty.com',
            'password' => bcrypt('Novelty'),
            'role' => 'usuario',
        ]);

    }
}

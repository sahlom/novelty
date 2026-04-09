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
            'name' => 'Benjamín Sahagún Lomelí',
            'display_name' => 'Benjamín Sahagún',
            'email' => 'benjamin@sahlom.com',
            'password' => bcrypt('Sahlom*2014.'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'María Guadalupe Patricia Navarro Ibarra',
            'display_name' => 'Patricia Navarro',
            'email' => 'direccion@gruponovelty.com',
            'password' => bcrypt('Novelty'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Ivan	Navarro Hernández',
            'display_name' => 'Ivan	Navarro',
            'email' => 'ivan.navarro@gruponovelty.com',
            'password' => bcrypt('Novelty'),
            'role' => 'usuario',
        ]);

        User::create([
            'name' => 'Gabriela Salomé Sánchez Romero',
            'display_name' => 'Gabriela Sánchez',
            'email' => 'asistente.direccion@gruponovelty.com',
            'password' => bcrypt('Novelty'),
            'role' => 'usuario',
        ]);

        User::create([
            'name' => 'Diana Laura Sánchez Romero',
            'display_name' => 'Diana Sánchez',
            'email' => 'recepcion@gruponovelty.com',
            'password' => bcrypt('Novelty'),
            'role' => 'usuario',
        ]);

        User::create([
            'name' => 'José Valentín Cárdenas Pérez',
            'display_name' => 'Valentín Cárdenas',
            'email' => 'auditoria@gruponovelty.com',
            'password' => bcrypt('Novelty'),
            'role' => 'usuario',
        ]);

        User::create([
            'name' => 'Martha Carolina López Arellano',
            'display_name' => 'Carolina López',
            'email' => 'contabilidad3@gruponovelty.com',
            'password' => bcrypt('Novelty'),
            'role' => 'usuario',
        ]);

        User::create([
            'name' => 'Janeth Guadalupe Terrones Sánchez',
            'display_name' => 'Janeth Terrones',
            'email' => 'contabilidad1@gruponovelty.com',
            'password' => bcrypt('Novelty'),
            'role' => 'usuario',
        ]);

        User::create([
            'name' => 'Gerardo Santos Velarde',
            'display_name' => 'Gerardo Santos',
            'email' => 'recursoshumanos@gruponovelty.com',
            'password' => bcrypt('Novelty'),
            'role' => 'usuario',
        ]);

        User::create([
            'name' => 'Valeria Getsemani Gámez González',
            'display_name' => 'Valeria Gámez',
            'email' => 'nominas1@gruponovelty.com',
            'password' => bcrypt('Novelty'),
            'role' => 'usuario',
        ]);

    }
}

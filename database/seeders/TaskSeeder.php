<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\Client;
use App\Models\User;
use App\Models\Area;
use App\Models\Status;
use App\Models\Priority;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        
        // Obtenemos IDs existentes para las relaciones
        $clientIds = Client::pluck('id')->toArray();
        $userIds = User::where('role', 'usuario')->pluck('id')->toArray();
        $areaIds = Area::pluck('id')->toArray();
        $statusIds = Status::pluck('id')->toArray();
        $priorityIds = Priority::pluck('id')->toArray();

        $tareasEjemplo = [
            'Falla en enlace de fibra óptica',
            'Configuración de VPN Site-to-Site',
            'Mantenimiento preventivo Servidor Dell',
            'Actualización de antivirus en contabilidad',
            'Revisión de cámaras de seguridad (DVR)',
            'Instalación de nodo de red en dirección',
            'Configuración de cuenta de correo Outlook',
            'Respaldo de base de datos SQL Server',
            'Limpieza física de racks',
            'Revisión de firewall Pfsense'
        ];

        foreach ($tareasEjemplo as $titulo) {
            Task::create([
                'client_id'   => $faker->randomElement($clientIds),
                'user_id'     => $faker->randomElement($userIds),
                'area_id'     => $faker->randomElement($areaIds),
                'status_id'   => $faker->randomElement($statusIds),
                'priority_id' => $faker->randomElement($priorityIds),
                'title'       => $titulo,
                'description' => $faker->paragraph(),
                'due_date'    => $faker->dateTimeBetween('now', '+1 month'),
            ]);
        }
    }
}

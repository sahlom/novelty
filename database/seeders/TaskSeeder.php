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
            'Cálculo y timbrado de nóminas mensuales',
            'Registro de asientos contables diarios',
            'Emisión de recibos de nómina oficiales',
            'Gestión de obligaciones con el IMSS',
            'Cálculo de retenciones de ISR trimestrales',
            'Elaboración del estado de resultados',
            'Conciliación bancaria mensual',
            'Cálculo y pago de finiquitos laborales',
            'Presentación de declaraciones fiscales mensuales',
            'Actualización del catálogo de cuentas'
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

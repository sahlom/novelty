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
        $faker = \Faker\Factory::create('es_MX'); // Descripciones e hilos de texto realistas
        
        // Obtenemos IDs existentes para las relaciones
        $clientIds = Client::pluck('id')->toArray();
        $userIds = User::where('role', 'usuario')->pluck('id')->toArray();
        
        // Salvaguarda: si no hay con rol 'usuario', usamos cualquier ID de usuario disponible
        if (empty($userIds)) {
            $userIds = User::pluck('id')->toArray();
        }
        
        $areaIds = Area::pluck('id')->toArray();
        $priorityIds = Priority::pluck('id')->toArray();

        // Mapeo dinámico del catálogo de estatus
        $statuses = Status::all();
        $closedKeywords = ['cerrada', 'cerrado', 'completada', 'completado', 'resuelta', 'resuelto'];
        
        $closedStatusIds = $statuses->filter(function($status) use ($closedKeywords) {
            return in_array(strtolower($status->name), $closedKeywords);
        })->pluck('id')->toArray();

        $openStatusIds = $statuses->filter(function($status) use ($closedKeywords) {
            return !in_array(strtolower($status->name), $closedKeywords);
        })->pluck('id')->toArray();

        // Respaldos de seguridad en caso de tablas de catálogos vacías
        $allStatusIds = $statuses->pluck('id')->toArray();
        if (empty($closedStatusIds)) { $closedStatusIds = [$allStatusIds[0] ?? 1]; }
        if (empty($openStatusIds)) { $openStatusIds = [$allStatusIds[0] ?? 1]; }

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
            'Actualización del catálogo de cuentas',
            'Auditoría interna de caja chica',
            'Revisión de facturación electrónica (CFDI)',
            'Declaración informativa de operaciones con terceros (DIOT)',
            'Trámite de devolución de saldos a favor de IVA',
            'Depuración de cuentas por cobrar'
        ];

        // ==================================================
        // 1. INYECCIÓN CONTROLADA: CLIENTE CON ID 7
        // ==================================================
        if (in_array(7, $clientIds)) {
            // 20 Tareas Abiertas específicas para Cliente 7
            for ($i = 1; $i <= 20; $i++) {
                Task::create([
                    'client_id'   => 7,
                    'user_id'     => $faker->randomElement($userIds),
                    'area_id'     => $faker->randomElement($areaIds),
                    'status_id'   => $faker->randomElement($openStatusIds),
                    'priority_id' => $faker->randomElement($priorityIds),
                    'title'       => $faker->randomElement($tareasEjemplo) . " (Abierta #$i)",
                    'description' => 'Seguimiento técnico operacional de bitácora. ' . $faker->paragraph(),
                    'due_date'    => $faker->dateTimeBetween('now', '+3 weeks'),
                ]);
            }

            // 15 Tareas Cerradas específicas para Cliente 7
            for ($j = 1; $j <= 15; $j++) {
                Task::create([
                    'client_id'   => 7,
                    'user_id'     => $faker->randomElement($userIds),
                    'area_id'     => $faker->randomElement($areaIds),
                    'status_id'   => $faker->randomElement($closedStatusIds),
                    'priority_id' => $faker->randomElement($priorityIds),
                    'title'       => $faker->randomElement($tareasEjemplo) . " (Cerrada #$j)",
                    'description' => 'Cierre de folio autorizado en el sistema de manera conforme. ' . $faker->paragraph(),
                    'due_date'    => $faker->dateTimeBetween('-2 months', 'now'),
                    'completed_at'=> $faker->dateTimeBetween('-1 month', 'now'),
                ]);
            }
        }

        // ==================================================
        // 2. INYECCIÓN GENERAL: 50 TAREAS COMPLEMENTARIAS
        // ==================================================
        for ($k = 1; $k <= 50; $k++) {
            Task::create([
                'client_id'   => $faker->randomElement($clientIds),
                'user_id'     => $faker->randomElement($userIds),
                'area_id'     => $faker->randomElement($areaIds),
                'status_id'   => $faker->randomElement($allStatusIds),
                'priority_id' => $faker->randomElement($priorityIds),
                'title'       => $faker->randomElement($tareasEjemplo) . " (Simulado Global #$k)",
                'description' => $faker->paragraph(),
                'due_date'    => $faker->dateTimeBetween('-1 week', '+1 month'),
            ]);
        }
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TaskComment;
use App\Models\Task;
use App\Models\User;

class TaskCommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        $taskIds = Task::pluck('id')->toArray();
        $userIds = User::pluck('id')->toArray(); // Admin o usuario pueden comentar

        $comentariosEjemplo = [
            'Nóminas timbradas y enviadas, todo correcto.',
            'Asientos contables registrados, sin observaciones.',
            'Recibos de nómina generados y entregados.',
            'Trámites IMSS actualizados y confirmados.',
            'Retenciones de ISR calculadas y declaradas.',
            'Estado de resultados listo para revisión.',
            'Conciliación bancaria concluida, saldos coinciden.',
            'Finiquitos procesados y pagados conforme.',
            'Declaraciones mensuales enviadas al SAT.',
            'Catálogo de cuentas actualizado y validado.'
        ];

        foreach ($comentariosEjemplo as $comentario) {
            TaskComment::create([
                'task_id' => $faker->randomElement($taskIds),
                'user_id' => $faker->randomElement($userIds),
                'body'    => $comentario,
            ]);
        }
    }
}

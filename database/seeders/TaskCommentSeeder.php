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
            'Se contactó al proveedor y reportan falla masiva.',
            'El equipo se llevó a taller para revisión profunda.',
            'Usuario confirma que ya tiene acceso a internet.',
            'Esperando piezas de repuesto para el servidor.',
            'Se realizó limpieza, se recomienda cambio de pasta térmica.',
            'Configuración completada, falta validar con el cliente.',
            'Se intentó llamar pero el cliente no responde.',
            'Respaldo verificado y guardado en disco externo.',
            'La falla persiste después de reiniciar el equipo.',
            'Tarea asignada al técnico de guardia.'
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

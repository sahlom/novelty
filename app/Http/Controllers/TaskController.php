<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Client;
use App\Models\Area;
use App\Models\Status;
use App\Models\Priority;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $query = Task::with(['client', 'area', 'status', 'priority', 'user']);

        if ($user->role === 'admin') {
            $tasks = $query->orderBy('priority_id', 'desc')->orderBy('created_at', 'asc')->get();
        } else {
            $tasks = $query->where('user_id', $user->id)->orderBy('priority_id', 'desc')->orderBy('created_at', 'asc')->get();
        }

        // Separación con nombre exacto: 'Completado'
        $closedTasks = $tasks->filter(function($task) {
            return trim($task->status->name) === 'Completado';
        });

        $openTasks = $tasks->reject(function($task) {
            return trim($task->status->name) === 'Completado';
        });

        return view('tasks.index', compact('openTasks', 'closedTasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = Client::all();
        $areas = Area::all();
        $statuses = Status::all();
        $priorities = Priority::all();

        // Solo traemos a los que tienen el rol de 'usuario' para asignarles la tarea
        $usuarios = User::where('role', 'usuario')->get();

        return view('tasks.create', compact('clients', 'areas', 'statuses', 'priorities', 'usuarios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id'   => 'required|exists:clients,id',
            'user_id'     => 'nullable|exists:users,id', // El responsable (rol usuario)
            'area_id'     => 'required|exists:areas,id',
            'status_id'   => 'required|exists:statuses,id',
            'priority_id' => 'required|exists:priorities,id',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'due_date'    => 'nullable|date',
        ]);

        // La fecha requested_at se llena automáticamente si usamos el default de la migración
        Task::create($validated);

        return redirect()->route('tasks.index')->with('success', 'La tarea ha sido creada y asignada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        // Cargamos las relaciones para ver quién es el cliente, área, etc.
        $task->load(['comments.user', 'client', 'user', 'area', 'status', 'priority']);
        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        // Seguridad: Si no es admin y no es su tarea, no entra
        if (auth()->user()->role !== 'admin' && auth()->id() !== $task->user_id) {
            abort(403, 'No tienes permiso para editar esta tarea.');
        }

        $clients = Client::all();
        $areas = Area::all();
        $statuses = Status::all();
        $priorities = Priority::all();
        $usuarios = User::where('role', 'usuario')->get();

        return view('tasks.edit', compact('task', 'clients', 'areas', 'statuses', 'priorities', 'usuarios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'client_id'   => 'required|exists:clients,id',
            'user_id'     => 'nullable|exists:users,id',
            'area_id'     => 'required|exists:areas,id',
            'status_id'   => 'required|exists:statuses,id',
            'priority_id' => 'required|exists:priorities,id',
            'description' => 'required|string',
            'due_date'    => 'nullable|date',
        ]);

        $task->update($validated);

        return redirect()->route('tasks.index')->with('success', 'Tarea actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        // Solo el admin puede eliminar tareas físicamente
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Solo el administrador puede eliminar registros.');
        }

        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Tarea eliminada.');
    }

    // public function dashboard()
    // {
    //     // Traemos tareas que no estén completadas para la pantalla general
    //     $tasks = Task::with(['client', 'area', 'status', 'priority', 'user'])
    //                 ->whereHas('status', function($q) {
    //                     $q->where('name', '!=', 'Completado');
    //                 })
    //                 ->orderBy('priority_id', 'desc')
    //                 ->get();

    //     return view('tasks.dashboard', compact('tasks'));
    // }


    public function dashboard()
    {
        // 1. Definir los nombres de tus estados cerrados (para excluir del conteo activo)
        $closedStatuses = ['cerrada', 'cerrado', 'completada', 'completado', 'resuelta', 'resuelto'];

        // 2. CONTADORES PRINCIPALES (TARJETAS)
        // Tareas Activas (Todo lo que NO esté cerrado)
        $activeTasksCount = \App\Models\Task::whereHas('status', function($query) use ($closedStatuses) {
            $query->whereNotIn(\DB::raw('LOWER(name)'), $closedStatuses);
        })->count();

        // Tareas En Proceso
        $processingTasksCount = \App\Models\Task::whereHas('status', function($query) {
            $query->where(\DB::raw('LOWER(name)'), 'like', '%proceso%')
                ->orWhere(\DB::raw('LOWER(name)'), 'like', '%atendiend%');
        })->count();

        // Tareas Urgentes o Altas (y que sigan activas)
        $urgentTasksCount = \App\Models\Task::whereHas('priority', function($query) {
            $query->where(\DB::raw('LOWER(name)'), 'like', '%urgent%')
                ->orWhere(\DB::raw('LOWER(name)'), 'like', '%alta%');
        })->whereHas('status', function($query) use ($closedStatuses) {
            $query->whereNotIn(\DB::raw('LOWER(name)'), $closedStatuses);
        })->count();

        // Clientes únicos con movimiento (que tienen tareas activas)
        $activeClientsCount = \App\Models\Task::whereHas('status', function($query) use ($closedStatuses) {
            $query->whereNotIn(\DB::raw('LOWER(name)'), $closedStatuses);
        })->distinct('client_id')->count('client_id');


        // 3. DATOS PARA GRÁFICA: Carga de Trabajo por Área
        $areasData = \App\Models\Task::whereHas('status', function($query) use ($closedStatuses) {
                $query->whereNotIn(\DB::raw('LOWER(name)'), $closedStatuses);
            })
            ->join('areas', 'tasks.area_id', '=', 'areas.id')
            ->select('areas.name as area_name', \DB::raw('count(tasks.id) as total'))
            ->groupBy('areas.name')
            ->get();

        $areasLabels = $areasData->pluck('area_name')->toArray();
        $areasValues = $areasData->pluck('total')->toArray();


        // 3.5 DATOS PARA GRÁFICA: Carga de Trabajo por Usuario (Formato Corto: Nombre A.)
        $usersData = \App\Models\Task::whereHas('status', function($query) use ($closedStatuses) {
                $query->whereNotIn(\DB::raw('LOWER(name)'), $closedStatuses);
            })
            ->join('users', 'tasks.user_id', '=', 'users.id')
            ->select(\DB::raw('COALESCE(users.display_name, users.name) as el_usuario'), \DB::raw('count(tasks.id) as total'))
            ->groupBy('users.display_name', 'users.name')
            ->get();

        // Procesamos los nombres uno por uno para recortarlos
        $usersLabels = $usersData->map(function ($item) {
            // Limpiamos espacios dobles por si acaso y dividimos por espacios
            $parts = explode(' ', preg_replace('/\s+/', ' ', trim($item->el_usuario)));
            
            if (count($parts) > 1) {
                // Tomamos el primer elemento (Nombre) y la inicial del segundo (Apellido) + un punto
                // return $parts[0] . ' ' . mb_substr($parts[1], 0, 1, 'UTF-8') . '.';
                return $parts[0] . ' ' . mb_substr($parts[1], 0, 1, 'UTF-8');
            }
            
            // Si el usuario solo tiene un nombre sin apellido registrado, lo dejamos igual
            return $parts[0];
        })->toArray();

        $usersValues = $usersData->pluck('total')->toArray();


        // 4. DATOS PARA GRÁFICA: Semáforo de Estados
        // Aquí mapeamos a 3 grandes grupos para la Dona
        $allActiveTasks = \App\Models\Task::whereHas('status', function($query) use ($closedStatuses) {
            $query->whereNotIn(\DB::raw('LOWER(name)'), $closedStatuses);
        })->with('status')->get();

        $nuevas = 0;
        $proceso = 0;
        $espera = 0;

        foreach ($allActiveTasks as $task) {
            $name = strtolower($task->status?->name ?? '');
            if (str_contains($name, 'nuev') || str_contains($name, 'pendient') || str_contains($name, 'registr')) {
                $nuevas++;
            } elseif (str_contains($name, 'proceso') || str_contains($name, 'atendiend')) {
                $proceso++;
            } elseif (str_contains($name, 'espera') || str_contains($name, 'detenid') || str_contains($name, 'pausa') || str_contains($name, 'atrasad')) {
                $espera++;
            }
        }
        $statusValues = [$nuevas, $proceso, $espera];


        // 5. DATOS PARA GRÁFICA: Top 5 Clientes con más carga activa
        $clientsData = \App\Models\Task::whereHas('status', function($query) use ($closedStatuses) {
                $query->whereNotIn(\DB::raw('LOWER(name)'), $closedStatuses);
            })
            ->join('clients', 'tasks.client_id', '=', 'clients.id')
            ->select('clients.razon_social as cliente', \DB::raw('count(tasks.id) as total'))
            ->groupBy('clients.razon_social')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        $clientsLabels = $clientsData->pluck('cliente')->toArray();
        $clientsValues = $clientsData->pluck('total')->toArray();


        // 6. TABLA: Las 5 Tareas más antiguas (Rezagadas)
        $oldestTasks = \App\Models\Task::whereHas('status', function($query) use ($closedStatuses) {
                $query->whereNotIn(\DB::raw('LOWER(name)'), $closedStatuses);
            })
            ->orderBy('created_at', 'asc')
            ->take(5)
            ->get();


        // Enviar todo a la vista
        return view('tasks.dashboard', compact(
            'activeTasksCount',
            'processingTasksCount',
            'urgentTasksCount',
            'activeClientsCount',
            'areasLabels',
            'areasValues',
            'statusValues',
            'clientsLabels',
            'clientsValues',
            'oldestTasks',
            'usersLabels',
            'usersValues'
        ));
    }

    public function monitor()
    {
        // Traemos tareas pendientes, en proceso o detenidas (excluimos completadas)
        $tasks = Task::with(['client', 'area', 'status', 'priority', 'user'])
                    ->whereHas('status', function($q) {
                        $q->where('name', '!=', 'Completado');
                    })
                    ->orderBy('priority_id', 'desc') // Prioridad primero
                    ->orderBy('created_at', 'asc')   // Luego las más antiguas
                    ->get();

        return view('tasks.monitor', compact('tasks'));
    }
}

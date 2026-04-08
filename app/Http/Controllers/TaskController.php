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
        // Obtenemos al usuario logueado actualmente
        $user = auth()->user();

        // Iniciamos una consulta con las relaciones para que no haga muchas peticiones a la DB
        $query = Task::with(['client', 'area', 'status', 'priority', 'user']);

        if ($user->role === 'admin') {
            // Si es admin, traemos todas las tareas de la base de datos
            $tasks = $query->latest()->get();
        } else {
            // Si es usuario, filtramos solo las tareas donde él sea el responsable
            $tasks = $query->where('user_id', $user->id)->latest()->get();
        }

        // Retornamos la vista enviando la colección de tareas
        return view('tasks.index', compact('tasks'));
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
        $task->load(['client', 'area', 'status', 'priority', 'user']);
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

    public function dashboard()
    {
        // Traemos tareas que no estén completadas para la pantalla general
        $tasks = Task::with(['client', 'area', 'status', 'priority', 'user'])
                    ->whereHas('status', function($q) {
                        $q->where('name', '!=', 'Completado');
                    })
                    ->orderBy('priority_id', 'desc')
                    ->get();

        return view('tasks.dashboard', compact('tasks'));
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

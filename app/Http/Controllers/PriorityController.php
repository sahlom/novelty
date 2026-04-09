<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Priority;

class PriorityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $priorities = Priority::all();
        return view('priorities.index', compact('priorities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('priorities.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate(['name' => 'required|string|max:50|unique:priorities,name',],
                                        ['name.unique' => 'Esta prioridad ya existe.',]);

        Priority::create($validated);

        return redirect()->route('priorities.index')->with('success', 'Prioridad creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Priority $priority)
    {
        return view('priorities.edit', compact('priority'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Priority $priority)
    {
        $validated = $request->validate(['name' => 'required|string|max:50|unique:priorities,name,' . $priority->id,],
                                        ['name.unique' => 'Ya existe otra prioridad con ese nombre.',]);

        $priority->update($validated);

        return redirect()->route('priorities.index')->with('success', 'Prioridad actualizada con éxito.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Priority $priority)
    {
        // 1. Validar Rol
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('priorities.index')->with('error', 'No tienes permisos para eliminar catálogos.');
        }

        // 2. Validar Integridad (Restrict)
        // Como no usamos 'set null', debemos obligar a que no existan tareas
        if ($priority->tasks()->exists()) {
            return redirect()->route('priorities.index')->with('error', 'No se puede eliminar: existen tareas asignadas a esta prioridad.');
        }

        $priority->delete();

        return redirect()->route('priorities.index')->with('success', 'Prioridad eliminada correctamente.');
    }
}

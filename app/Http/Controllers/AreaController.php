<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $areas = Area::all();
        return view('areas.index', compact('areas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('areas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:areas,name',
        ]);

        Area::create($request->all());

        return redirect()->route('areas.index')->with('success', 'Área creada con éxito.');
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
    public function edit(Area $area)
    {
        return view('areas.edit', compact('area'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Area $area)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:areas,name,' . $area->id,
        ]);

        $area->update($request->all());

        return redirect()->route('areas.index')->with('success', 'Área actualizada.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Area $area)
    {
        // 1. Verificación de Seguridad: Solo el Admin puede borrar catálogos
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('areas.index')
                ->with('error', 'No tienes permisos suficientes para realizar esta acción.');
        }

        // 2. Verificación de Integridad: ¿Qué pasa con las tareas existentes?
        $taskCount = $area->tasks()->count();

        if ($taskCount > 0) {
            // Opción A: Bloqueo total (Más seguro para TICs)
            return redirect()->route('areas.index')
                ->with('error', "No se puede eliminar: El área '{$area->name}' tiene {$taskCount} tareas asociadas. Reasígnalas antes de borrar.");
                
            /* // Opción B: Si prefieres permitir el borrado sabiendo que se pondrán en NULL:
            $area->delete();
            return redirect()->route('areas.index')
                ->with('info', "Área eliminada. {$taskCount} tareas quedaron ahora como 'Sin Área'.");
            */
        }

        // 3. Borrado final si no hay tareas
        $area->delete();

        return redirect()->route('areas.index')
            ->with('success', 'Área eliminada correctamente.');
        }
}

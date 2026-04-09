<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Status;

class StatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $statuses = Status::all();
        return view('statuses.index', compact('statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('statuses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:statuses,name',
        ]);

        Status::create($validated);
        return redirect()->route('statuses.index')->with('success', 'Estatus creado.');
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
    public function edit(Status $status)
    {
        return view('statuses.edit', compact('status'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Status $status)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:statuses,name,' . $status->id,
        ]);

        $status->update($validated);
        return redirect()->route('statuses.index')->with('success', 'Estatus actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Status $status)
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('statuses.index')->with('error', 'No autorizado.');
        }

        if ($status->tasks()->exists()) {
            return redirect()->route('statuses.index')->with('error', 'No se puede borrar: hay tareas con este estatus.');
        }

        $status->delete();
        return redirect()->route('statuses.index')->with('success', 'Estatus eliminado.');
    }
}

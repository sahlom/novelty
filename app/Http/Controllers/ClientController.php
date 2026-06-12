<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    /**
     * Muestra la lista de clientes.
     */
    public function index()
    {
        $clients = Client::latest()->get(); // Trae los últimos registros primero
        return view('clients.index', compact('clients'));
    }

    /**
     * Muestra el formulario para crear un cliente.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Guarda un nuevo cliente con sus datos y vigencias.
     */
    public function store(Request $request)
    {
        $request->validate([
            'razon_social' => 'required|string|max:255',
            'contacto' => 'nullable|string|max:255',
            'rfc' => [
                'required',
                'string',
                'regex:/^([A-ZÑ&]{3,4}) ?(?:\d{2})(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01]) ?(?:[A-Z\d]{2})([A\d])$/i',
                'unique:clients,rfc'
            ],
            'tel' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'fiel_vigencia' => 'nullable|date',
            'csd_vigencia' => 'nullable|date',
        ], [
            'rfc.regex' => 'El formato del RFC no es válido para México.',
            'rfc.unique' => 'Este RFC ya está registrado en el sistema.'
        ]);

        Client::create([
            // Aplicamos la limpieza automática de las reglas del SAT
            'razon_social' => $this->cleanRazonSocial($request->razon_social),
            'contacto' => $request->contacto,
            'rfc' => strtoupper(str_replace(' ', '', $request->rfc)),
            'tel' => $request->tel,
            'email' => $request->email,
            'fiel_vigencia' => $request->fiel_vigencia,
            'csd_vigencia' => $request->csd_vigencia,
            'csf' => false,
            'opinion_cumplimiento' => false,
            'fiel' => false,
            'csd' => false,
        ]);

        return redirect()->route('clients.index')->with('success', 'Cliente creado con éxito.');
    }
    /**
     * Muestra el expediente detallado del cliente.
     */
    public function show($id)
    {
        // Cargamos el cliente y todas las relaciones anidadas necesarias de la tarea
        $client = Client::with(['tasks.status', 'tasks.area', 'tasks.user', 'tasks.priority'])->findOrFail($id);

        return view('clients.show', compact('client'));
    }

    /**
     * Muestra el formulario de edición.
     */
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    /**
     * Actualiza el expediente en la base de datos.
     */
    public function update(Request $request, Client $client)
    {
        $request->validate([
            'razon_social' => 'required|string|max:255',
            'contacto' => 'nullable|string|max:255',
            'rfc' => [
                'required',
                'string',
                'regex:/^([A-ZÑ&]{3,4}) ?(?:\d{2})(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01]) ?(?:[A-Z\d]{2})([A\d])$/i',
                Rule::unique('clients', 'rfc')->ignore($client->id)
            ],
            'tel' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'fiel_vigencia' => 'nullable|date',
            'csd_vigencia' => 'nullable|date',
        ], [
            'rfc.regex' => 'El formato del RFC no es válido.',
        ]);

        $client->update([
            // Aplicamos la limpieza automática también al actualizar
            'razon_social' => $this->cleanRazonSocial($request->razon_social),
            'contacto' => $request->contacto,
            'rfc' => strtoupper(str_replace(' ', '', $request->rfc)),
            'tel' => $request->tel,
            'email' => $request->email,
            'fiel_vigencia' => $request->fiel_vigencia,
            'csd_vigencia' => $request->csd_vigencia,
        ]);

        return redirect()->route('clients.index')->with('success', 'Expediente actualizado correctamente.');
    }

    /**
     * Elimina un cliente.
     */
    public function destroy(Client $client)
    {
        // Validación de seguridad por si tiene tareas asignadas en un futuro
        if ($client->tasks()->exists()) {
            return redirect()->route('clients.index')->with('error', 'No se puede eliminar este cliente porque tiene tareas o incidencias asociadas.');
        }

        $client->delete();
        return redirect()->route('clients.index')->with('success', 'Cliente eliminado correctamente.');
    }

    /**
     * Método privado para estandarizar la Razón Social bajo reglas del SAT.
     */
    private function cleanRazonSocial(string $string): string
    {
        // 1. Convertir a mayúsculas limpiando caracteres especiales de codificación
        $string = mb_strtoupper(trim($string), 'UTF-8');

        // 2. Mapeo para eliminar acentos (Respetando la Ñ y la Diéresis si el SAT las usa, aunque acentos no)
        $buscar   = ['Á', 'É', 'Í', 'Ó', 'Ú', 'Ü'];
        $reemplazar = ['A', 'E', 'I', 'O', 'U', 'U'];
        $string = str_replace($buscar, $reemplazar, $string);

        // 3. Quitar puntos y comas (comunes en "S.A., DE C.V." o "S.A. DE C.V.")
        $string = str_replace(['.', ','], '', $string);

        // Eliminar espacios dobles remanentes
        $string = preg_replace('/\s+/', ' ', $string);

        return trim($string);
    }
}
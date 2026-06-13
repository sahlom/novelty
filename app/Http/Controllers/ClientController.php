<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

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
     * Sube un archivo privado del SAT (.pdf o .zip) de forma blindada.
     */
    public function uploadDocument(Request $request, $id, $type)
    {
        // 1. Validar que el tipo coincida exactamente con tus columnas
        $allowedTypes = ['csf', 'opinion', 'fiel', 'csd'];
        if (!in_array($type, $allowedTypes)) {
            return back()->with('error', 'El tipo de documento solicitado no es válido.');
        }

        // 2. Reglas de validación dinámicas según el formato del archivo y requerimiento de vigencia
        if ($type === 'fiel' || $type === 'csd') {
            // Los paquetes de firmas requieren estrictamente el .zip Y la fecha de vencimiento
            $request->validate([
                'documento' => 'required|file|mimes:zip|max:5120', // Máximo 5MB
                'vigencia'  => 'required|date', // Obligatorio para asegurar consistencia
            ], [
                'documento.mimes' => 'El paquete de la ' . strtoupper($type) . ' debe ser estrictamente un archivo comprimido .zip',
                'documento.max'   => 'El archivo no debe pesar más de 5MB.',
                'vigencia.required' => 'La fecha de vencimiento es obligatoria para registrar la ' . strtoupper($type) . '.',
                'vigencia.date'     => 'Introduce un formato de fecha válido.',
            ]);
        } else {
            // La Constancia (csf) u Opinión solo requieren el PDF
            $request->validate([
                'documento' => 'required|file|mimes:pdf|max:4096', // Máximo 4MB
            ], [
                'documento.mimes' => 'Este documento debe ser estrictamente un archivo en formato PDF.',
                'documento.max'   => 'El archivo no debe pesar más de 4MB.',
            ]);
        }

        $client = Client::findOrFail($id);

        if ($request->hasFile('documento')) {
            $columnName = $type;

            // 3. Limpieza: Si ya existía un archivo previo, lo borramos para evitar basura en el servidor
            if ($client->$columnName && Storage::disk('local')->exists($client->$columnName)) {
                Storage::disk('local')->delete($client->$columnName);
            }

            // 4. Guardar físicamente en: storage/app/clientes/{id_del_cliente}/
            $file = $request->file('documento');
            $fileName = $type . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs("clientes/{$client->id}", $fileName, 'local');

            // 5. Preparar los datos para la actualización masiva
            $updateData = [
                $columnName => $path
            ];

            // Si el tipo maneja vigencia y viene en el request, la agregamos dinámicamente al array
            if (($type === 'fiel' || $type === 'csd') && $request->filled('vigencia')) {
                $columnVigencia = $type . '_vigencia'; // 'fiel_vigencia' o 'csd_vigencia'
                $updateData[$columnVigencia] = $request->input('vigencia');
            }

            // 6. Actualizamos el registro en la base de datos
            $client->update($updateData);

            return back()->with('success', 'Archivo ' . strtoupper($type) . ' y expediente actualizados correctamente.');
        }

        return back()->with('error', 'No se detectó ningún archivo para subir.');
    }

    /**
     * Descarga el archivo (.pdf o .zip) pasando obligatoriamente por la validación de seguridad.
     */
    public function downloadDocument($id, $type)
    {
        // Aseguramos que solo busquen columnas válidas
        $allowedTypes = ['csf', 'opinion', 'fiel', 'csd'];
        if (!in_array($type, $allowedTypes)) {
            abort(404);
        }

        $client = Client::findOrFail($id);
        $columnName = $type;
        $path = $client->$columnName;

        // Si el registro tiene una ruta guardada y el archivo realmente existe en storage/app/
        if ($path && Storage::disk('local')->exists($path)) {
            // Laravel lee el archivo desde la zona oculta y lo sirve de forma segura para descarga
            return Storage::disk('local')->download($path);
        }

        return back()->with('error', 'El archivo solicitado no se encuentra en el servidor o fue eliminado.');
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
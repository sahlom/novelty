<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        // 1. Validaciones fusionadas (Entradas del formulario HTML + Reglas fiscales)
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
            
            // Coerción estricta backend utilizando el nombre del input HTML
            'fiel_vigencia' => 'required_with:file_fiel|nullable|date',
            'csd_vigencia'  => 'required_with:file_csd|nullable|date',
            
            // Validación de tipos y pesos provenientes del formulario
            'file_fiel'     => 'nullable|file|mimes:zip|max:10240', // max 10MB
            'file_csd'      => 'nullable|file|mimes:zip|max:10240',
            'file_csf'      => 'nullable|file|mimes:pdf|max:5120',  // max 5MB
            'file_opinion'  => 'nullable|file|mimes:pdf|max:5120',
        ], [
            'rfc.regex' => 'El formato del RFC no es válido para México.',
            'rfc.unique' => 'Este RFC ya está registrado en el sistema.',
            'fiel_vigencia.required_with' => 'Es obligatorio indicar la fecha de vigencia si se adjunta el paquete FIEL.',
            'csd_vigencia.required_with' => 'Es obligatorio indicar la fecha de vigencia si se adjuntan los sellos CSD.',
            'file_fiel.mimes' => 'El archivo de la FIEL debe ser un paquete comprimido (.zip).',
            'file_csd.mimes' => 'El archivo del CSD debe ser un paquete comprimido (.zip).',
            'file_csf.mimes' => 'La Constancia de Situación Fiscal debe ser un archivo PDF.',
            'file_opinion.mimes' => 'La Opinión de Cumplimiento debe ser un archivo PDF.',
        ]);

        // Iniciamos transacción para evitar registros huérfanos si la carga en disco falla
        DB::beginTransaction();

        try {
            // 2. Preparar los datos limpios de texto plano
            $rfcLimpio = strtoupper(str_replace(' ', '', $request->rfc));
            $razonSocialLimpia = $this->cleanRazonSocial($request->razon_social);

            // 3. Primer guardado: Apartamos el registro base en la DB para disparar el ID autoincremental
            $client = Client::create([
                'razon_social'  => $razonSocialLimpia,
                'contacto'      => $request->contacto,
                'rfc'           => $rfcLimpio,
                'tel'           => $request->tel,
                'email'         => $request->email,
                'fiel_vigencia' => $request->fiel_vigencia ? Carbon::parse($request->fiel_vigencia) : null,
                'csd_vigencia'  => $request->csd_vigencia ? Carbon::parse($request->csd_vigencia) : null,
                // Inicializados en null, respetando tus nombres de columna en el $fillable
                'csf'           => null,
                'opinion'       => null,
                'fiel'          => null,
                'csd'           => null,
            ]);

            // 4. Homologación estricta de rutas físicas basadas en el ID obtenido
            // Cambiamos a la estructura uniforme: storage/app/clientes/{id}/
            $folderPath = 'clientes/' . $client->id;

            // 5. Almacenamiento físico de binarios e inyección de rutas al modelo instanciado
            if ($request->hasFile('file_fiel')) {
                $client->fiel = $request->file('file_fiel')->storeAs($folderPath, 'fiel_' . time() . '.zip', 'local');
            }

            if ($request->hasFile('file_csd')) {
                $client->csd = $request->file('file_csd')->storeAs($folderPath, 'csd_' . time() . '.zip', 'local');
            }

            if ($request->hasFile('file_csf')) {
                $client->csf = $request->file('file_csf')->storeAs($folderPath, 'csf_' . time() . '.pdf', 'local');
            }

            if ($request->hasFile('file_opinion')) {
                $client->opinion = $request->file('file_opinion')->storeAs($folderPath, 'opinion_' . time() . '.pdf', 'local');
            }

            // 6. Segundo guardado: Consolidar y persistir las cadenas de los paths en la fila correspondiente
            $client->save();

            DB::commit(); // Confirmamos los cambios de forma permanente en Base de Datos y Disco

            // 7. Redirección final al panel indexado
            return redirect()->route('clients.index')->with('success', 'Cliente y expediente creados con éxito.');

        } catch (\Exception $e) {
            DB::rollBack(); // Revierte el SQL si ocurre un error inesperado al escribir en el storage
            return redirect()->back()->withInput()->withErrors(['error' => 'Error al procesar el expediente del cliente: ' . $e->getMessage()]);
        }
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
    public function edit($id)
    {
        $client = Client::findOrFail($id);
        return view('clients.edit', compact('client'));
    }

    /**
     * Actualiza el expediente en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        // 1. Validaciones (El RFC ignora el ID actual para que no diga "ya está registrado")
        $request->validate([
            'razon_social' => 'required|string|max:255',
            'contacto' => 'nullable|string|max:255',
            'rfc' => [
                'required',
                'string',
                'regex:/^([A-ZÑ&]{3,4}) ?(?:\d{2})(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01]) ?(?:[A-Z\d]{2})([A\d])$/i',
                'unique:clients,rfc,' . $client->id // <--- Ignora este registro
            ],
            'tel' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            
            // Mismas reglas de archivos que usamos en el store
            'fiel_vigencia' => 'required_with:file_fiel|nullable|date',
            'csd_vigencia'  => 'required_with:file_csd|nullable|date',
            'file_fiel'     => 'nullable|file|mimes:zip|max:5120', 
            'file_csd'      => 'nullable|file|mimes:zip|max:5120',
            'file_csf'      => 'nullable|file|mimes:pdf|max:4096',  
            'file_opinion'  => 'nullable|file|mimes:pdf|max:4096',
        ], [
            'rfc.regex' => 'El formato del RFC no es válido para México.',
            'rfc.unique' => 'Este RFC ya está registrado en el sistema.',
        ]);

        // 2. Limpieza de textos
        $rfcLimpio = strtoupper(str_replace(' ', '', $request->rfc));
        $razonSocialLimpia = $this->cleanRazonSocial($request->razon_social);

        // 3. Actualizar campos de texto plano y fechas primero
        $client->razon_social = $razonSocialLimpia;
        $client->contacto     = $request->contacto;
        $client->rfc          = $rfcLimpio;
        $client->tel          = $request->tel;
        $client->email        = $request->email;
        
        if ($request->filled('fiel_vigencia')) {
            $client->fiel_vigencia = Carbon::parse($request->fiel_vigencia);
        }
        if ($request->filled('csd_vigencia')) {
            $client->csd_vigencia = Carbon::parse($request->csd_vigencia);
        }

        // 4. Procesar archivos (Si suben uno nuevo, borramos el viejo e insertamos con time())
        $folderPath = "clientes/{$client->id}";

        // Bloque FIEL
        if ($request->hasFile('file_fiel')) {
            if ($client->fiel && Storage::disk('local')->exists($client->fiel)) {
                Storage::disk('local')->delete($client->fiel);
            }
            $client->fiel = $request->file('file_fiel')->storeAs($folderPath, 'fiel_' . time() . '.zip', 'local');
        }

        // Bloque CSD
        if ($request->hasFile('file_csd')) {
            if ($client->csd && Storage::disk('local')->exists($client->csd)) {
                Storage::disk('local')->delete($client->csd);
            }
            $client->csd = $request->file('file_csd')->storeAs($folderPath, 'csd_' . time() . '.zip', 'local');
        }

        // Bloque CSF
        if ($request->hasFile('file_csf')) {
            if ($client->csf && Storage::disk('local')->exists($client->csf)) {
                Storage::disk('local')->delete($client->csf);
            }
            $client->csf = $request->file('file_csf')->storeAs($folderPath, 'csf_' . time() . '.pdf', 'local');
        }

        // Bloque Opinión
        if ($request->hasFile('file_opinion')) {
            if ($client->opinion && Storage::disk('local')->exists($client->opinion)) {
                Storage::disk('local')->delete($client->opinion);
            }
            $client->opinion = $request->file('file_opinion')->storeAs($folderPath, 'opinion_' . time() . '.pdf', 'local');
        }

        // 5. Guardar todos los cambios
        $client->save();

        return redirect()->route('clients.index')->with('success', 'Cliente actualizado con éxito.');
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
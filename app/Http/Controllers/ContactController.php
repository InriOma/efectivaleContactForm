<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    private function getMensajes()
    {
        $mensajes = [];
        
        if (Storage::exists('mensajes.json')) {
            $contenido = Storage::get('mensajes.json');
            $mensajes = json_decode($contenido, true);
            
            // Asegurar que $mensajes sea siempre un array
            if (!is_array($mensajes)) {
                $mensajes = [];
            }
        }
        
        return $mensajes;
    }

    private function saveMensajes($mensajes)
    {
        Storage::put('mensajes.json', json_encode($mensajes, JSON_PRETTY_PRINT));
    }

    public function index()
    {
        $mensajes = $this->getMensajes();
        return view('contact.index', compact('mensajes'));
    }

    public function store(Request $request)
    {
        // Validación del servidor
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mensaje' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Preparar datos
        $nuevoMensaje = [
            'id' => uniqid(),
            'nombre' => $request->nombre,
            'email' => $request->email,
            'mensaje' => $request->mensaje,
            'fecha' => now()->format('Y-m-d H:i:s')
        ];

        // Leer archivo existente o crear array vacío
        $mensajes = [];
        if (Storage::exists('mensajes.json')) {
            $contenido = Storage::get('mensajes.json');
            $mensajes = json_decode($contenido, true) ?? [];
        }

        // Agregar nuevo mensaje
        $mensajes[] = $nuevoMensaje;

        // Guardar en archivo
        $this->saveMensajes($mensajes);

        return response()->json([
            'success' => true,
            'message' => 'Mensaje guardado exitosamente'
        ]);
    }

    public function registros()
    {
        $mensajes = $this->getMensajes();
        return view('contact.registros', compact('mensajes'));
    }

    public function destroy($id)
    {
        $mensajes = $this->getMensajes();

        // Filtrar el mensaje a eliminar
        $mensajes = array_filter($mensajes, function($mensaje) use ($id) {
            return isset($mensaje['id']) && $mensaje['id'] !== $id;
        });

        // Reindexar el array
        $mensajes = array_values($mensajes);

        // Guardar los mensajes actualizados
        $this->saveMensajes($mensajes);

        return response()->json([
            'success' => true,
            'message' => 'Mensaje eliminado exitosamente'
        ]);
    }

    public function clear()
    {
        // Eliminar el archivo de mensajes
        if (Storage::exists('mensajes.json')) {
            Storage::delete('mensajes.json');
        }

        return response()->json([
            'success' => true,
            'message' => 'Todos los mensajes han sido eliminados'
        ]);
    }

    public function edit($id)
    {
        $mensajes = $this->getMensajes();
        
        $mensaje = collect($mensajes)->firstWhere('id', $id);
        
        if (!$mensaje) {
            return response()->json([
                'success' => false,
                'message' => 'Mensaje no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $mensaje
        ]);
    }

    public function update(Request $request, $id)
    {
        // Validación del servidor
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mensaje' => 'required|string|max:1000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $mensajes = $this->getMensajes();
        
        // Encontrar y actualizar el mensaje
        $mensajeIndex = null;
        foreach ($mensajes as $index => $mensaje) {
            if (isset($mensaje['id']) && $mensaje['id'] === $id) {
                $mensajeIndex = $index;
                break;
            }
        }

        if ($mensajeIndex === null) {
            return response()->json([
                'success' => false,
                'message' => 'Mensaje no encontrado'
            ], 404);
        }

        // Actualizar el mensaje manteniendo id y fecha original
        $mensajes[$mensajeIndex]['nombre'] = $request->nombre;
        $mensajes[$mensajeIndex]['email'] = $request->email;
        $mensajes[$mensajeIndex]['mensaje'] = $request->mensaje;
        $mensajes[$mensajeIndex]['fecha_modificacion'] = now()->format('Y-m-d H:i:s');

        // Guardar cambios
        $this->saveMensajes($mensajes);

        return response()->json([
            'success' => true,
            'message' => 'Mensaje actualizado exitosamente'
        ]);
    }
}
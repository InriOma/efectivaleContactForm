<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact.index');
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
        Storage::put('mensajes.json', json_encode($mensajes, JSON_PRETTY_PRINT));

        return response()->json([
            'success' => true,
            'message' => 'Mensaje guardado exitosamente'
        ]);
    }

    public function registros()
    {
        $mensajes = [];
        
        if (Storage::exists('mensajes.json')) {
            $contenido = Storage::get('mensajes.json');
            $mensajes = json_decode($contenido, true) ?? [];
        }

        return view('contact.registros', compact('mensajes'));
    }
}

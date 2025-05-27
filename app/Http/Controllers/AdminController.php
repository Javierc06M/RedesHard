<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin;
use App\Models\Ventas;

class AdminController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();

        // Obtiene las ventas del admin autenticado
        $ventas = $admin->ventas()->latest()->get(); // ordenadas por fecha descendente

        return view('admin.profile.index', compact('admin', 'ventas'));
    }

     public function update(Request $request)
    {
        // Validación básica
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'username' => 'required|string|max:255',
            'imagen' => 'nullable|image|max:5120', // max 5MB
        ]);

        if ($validator->fails()) {
            // Registrar errores
            Log::error('Error de validación en actualización de perfil:', $validator->errors()->toArray());
            
            return response()->json([
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $admin = auth('admin')->user();

            $admin->nombre = $request->input('nombre');
            $admin->apellidos = $request->input('apellido');
            $admin->email = $request->input('email');
            $admin->username = $request->input('username');

            if ($request->hasFile('imagen')) {
                $image = $request->file('imagen');
                $path = $image->store('admin_images', 'public');  // guarda en storage/app/public/admin_images y retorna 'admin_images/archivo.jpg'

                $admin->imagen = $path;
            }

            $admin->save();

            return response()->json([
                'message' => 'Perfil actualizado correctamente',
                'imageUrl' => $admin->imagen ? asset(str_replace('public/', 'storage/', $admin->imagen)) : null
            ]);

        } catch (\Exception $e) {
            // Registrar excepción en laravel.log
            Log::error('Error al actualizar perfil: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error al actualizar perfil'
            ], 500);
        }
    }

}

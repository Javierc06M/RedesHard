<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Admin;

class AuthController extends Controller
{

    public function showLoginForm()
    {
        if (auth()->check()) {
            return redirect()->route('ventas.index'); // Cambia por tu ruta principal
        }

        return view('login.index');
    }


    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'username' => ['required', 'string'],
                'password' => ['required', 'string'],
            ]);

            // Intentar iniciar sesión usando el guard 'admin'
            if (Auth::guard('admin')->attempt([
                'username' => $credentials['username'],
                'password' => $credentials['password']
            ], $request->filled('remember'))) {
                
                $request->session()->regenerate();

                // Log opcional de inicio exitoso
                Log::info('Admin inició sesión', ['username' => $credentials['username']]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Inicio de sesión exitoso'
                ]);
            }

            // Registrar intento fallido
            Log::warning('Intento de inicio de sesión fallido', [
                'username' => $credentials['username'],
                'ip' => $request->ip(),
                'time' => now()->toDateTimeString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Credenciales incorrectas'
            ], 401);

        } catch (\Exception $e) {
            // Log de error inesperado
            Log::error('Error durante el intento de login', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Ocurrió un error inesperado al iniciar sesión.'
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        // Cierra la sesión del usuario autenticado
        Auth::logout();

        // Invalida la sesión actual para evitar reutilización
        $request->session()->invalidate();

        // Regenera el token CSRF para seguridad
        $request->session()->regenerateToken();

        // Redirige al usuario a la página de login después de cerrar sesión
        return redirect()->route('login.index');
    }

}

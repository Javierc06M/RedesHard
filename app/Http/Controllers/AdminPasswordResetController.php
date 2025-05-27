<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminPasswordResetController extends Controller
{
      public function showForgotForm()
    {
        return view('login.email');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin) {
            return back()->withErrors(['email' => 'Este correo no está registrado.']);
        }

        $token = Str::random(64);

        DB::table('admin_password_resets')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $token, 'created_at' => Carbon::now()]
        );

        $resetLink = url('/admin/password/reset/' . $token);

        Mail::raw("Haz clic en el siguiente enlace para restablecer tu contraseña:\n\n$resetLink", function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('Recuperación de contraseña de Admin');
        });

        return back()->with('status', 'Se envió el enlace de recuperación al correo.');
    }

    public function showResetForm($token)
    {
        return view('login.reset', ['token' => $token]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|confirmed|min:6',
        ]);

        $tokenData = DB::table('admin_password_resets')
            ->where('token', $request->token)
            ->where('email', $request->email)
            ->first();

        if (!$tokenData || Carbon::parse($tokenData->created_at)->addMinutes(60)->isPast()) {
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'El token es inválido o ha expirado.'], 400);
            }
            return back()->withErrors(['email' => 'El token es inválido o ha expirado.']);
        }

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin) {
            if ($request->ajax()) {
                return response()->json(['status' => 'error', 'message' => 'No se encontró un admin con ese correo.'], 404);
            }
            return back()->withErrors(['email' => 'No se encontró un admin con ese correo.']);
        }

        $admin->password = Hash::make($request->password);
        $admin->save();

        DB::table('admin_password_resets')->where('email', $request->email)->delete();

        if ($request->ajax()) {
            return response()->json(['status' => 'success', 'message' => 'Contraseña actualizada correctamente.']);
        }

        return redirect()->route('login.index')->with('status', 'Contraseña actualizada correctamente.');
    }
}

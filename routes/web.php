<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminForgotPasswordController;
use App\Http\Middleware\AdminAuth;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Controllers\AdminPasswordResetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VentasController;


    Route::middleware([RedirectIfAuthenticated::class])->group(function () {
        Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.index');
        Route::post('/login', [AuthController::class, 'login']) -> name('login.submit');
    });

    //RUTAS PARA RECUPERAR LA CONTRASEÑA
    Route::get('/admin/password/forgot', [AdminPasswordResetController::class, 'showForgotForm'])->name('admin.email');
    Route::post('/admin/password/send', [AdminPasswordResetController::class, 'sendResetLink'])->name('admin.password.send');

    Route::get('/admin/password/reset/{token}', [AdminPasswordResetController::class, 'showResetForm'])->name('login.reset');
    Route::post('/admin/password/update', [AdminPasswordResetController::class, 'resetPassword'])->name('admin.password.update');



    Route::middleware([AdminAuth::class])->group(function () {

        //CERRAR SESION
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


        //RUTAS PARA LAS VENTAS
        Route::get('/', [VentasController::class, 'index']) -> name('ventas.index');
        Route::post('/ventas', [VentasController::class, 'store'])->name('ventas.store');
        Route::delete('/ventas/{id}', [VentasController::class, 'destroy'])->name('ventas.destroy');
        Route::put('/ventas/{id}', [VentasController::class, 'update'])->name('ventas.update');



        // RUTAS PARA EL PERFIL DEL ADMIN
        Route::get('/admin/profile', [AdminController::class, 'index']) -> name('admin.profile');
        Route::post('/admin/profile/update', [AdminController::class, 'update'])->name('admin.profile.update');


    });
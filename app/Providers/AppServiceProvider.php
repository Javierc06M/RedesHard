<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use App\Models\Ventas;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.admin', function ($view) {
            $now = Carbon::now();
            $mesActual = $now->month;
            $anioActual = $now->year;
            $mesAnterior = $now->copy()->subMonth();

            $totalActual = Ventas::whereMonth('fecha', $mesActual)
                                ->whereYear('fecha', $anioActual)
                                ->sum('costo');

            $totalAnterior = Ventas::whereMonth('fecha', $mesAnterior->month)
                                ->whereYear('fecha', $mesAnterior->year)
                                ->sum('costo');

            $porcentajeCambio = $totalAnterior > 0
                ? (($totalActual - $totalAnterior) / $totalAnterior) * 100
                : 0;

            $productosActual = Ventas::whereMonth('fecha', $mesActual)
                                    ->whereYear('fecha', $anioActual)
                                    ->count();

            $productosAnterior = Ventas::whereMonth('fecha', $mesAnterior->month)
                                    ->whereYear('fecha', $mesAnterior->year)
                                    ->count();

            $variacionProductos = $productosAnterior > 0
                ? (($productosActual - $productosAnterior) / $productosAnterior) * 100
                : 0;

            $view->with([
                'totalActual' => $totalActual,
                'porcentajeCambio' => $porcentajeCambio,
                'productosActual' => $productosActual,
                'variacionProductos' => $variacionProductos,
            ]);
        });
    }
}

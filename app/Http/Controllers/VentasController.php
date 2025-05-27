<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Models\Admin;
use App\Models\Ventas;
use Carbon\Carbon;

class VentasController extends Controller
{
    public function index(Request $request)
    {
        $now = Carbon::now();
        $mesActual = $now->month;
        $anioActual = $now->year;

        // Ventas paginadas solo del mes actual
        $ventas = Ventas::whereMonth('fecha', $mesActual)
                        ->whereYear('fecha', $anioActual)
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

        // Mes anterior para comparación
        $mesAnterior = $now->copy()->subMonth();

        // Total de ventas en soles del mes actual
        $totalActual = Ventas::whereMonth('fecha', $mesActual)
                            ->whereYear('fecha', $anioActual)
                            ->sum('costo');

        // Total de ventas en soles del mes anterior
        $totalAnterior = Ventas::whereMonth('fecha', $mesAnterior->month)
                            ->whereYear('fecha', $mesAnterior->year)
                            ->sum('costo');

        // Cálculo del porcentaje de cambio de ingresos
        $porcentajeCambio = 0;
        if ($totalAnterior > 0) {
            $porcentajeCambio = (($totalActual - $totalAnterior) / $totalAnterior) * 100;
        }

        // Conteo de productos vendidos (cantidad de registros) mes actual y anterior
        $productosActual = Ventas::whereMonth('fecha', $mesActual)
                                ->whereYear('fecha', $anioActual)
                                ->count();

        $productosAnterior = Ventas::whereMonth('fecha', $mesAnterior->month)
                                ->whereYear('fecha', $mesAnterior->year)
                                ->count();

        // Cálculo de la variación porcentual de productos vendidos
        $variacionProductos = 0;
        if ($productosAnterior > 0) {
            $variacionProductos = (($productosActual - $productosAnterior) / $productosAnterior) * 100;
        }

        if ($request->ajax()) {
            return view('layouts.partials.ventas-table', compact('ventas'))->render();
        }

        // Enviamos las variables a la vista
        return view('admin.cliente.index', compact(
            'ventas',
            'totalActual',
            'porcentajeCambio',
            'productosActual',
            'variacionProductos'
        ));
    }


   public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'fecha' => 'required|date',
                'nombre' => 'required|string|max:255',
                'telefono' => 'required|string|max:20',
                'descripcion' => 'nullable|string',
                'costo' => 'required|numeric',
                'tipo_pago' => 'required|string',
            ]);

            $adminId = Auth::guard('admin')->id();
            if (!$adminId) {
                return response()->json([
                    'message' => 'No hay administrador autenticado.'
                ], 401);
            }

            $now = Carbon::now();  // Aquí obtiene la fecha y hora local según config/app.php

            Ventas::create([
                'fecha' => $validated['fecha'],
                'nombre' => $validated['nombre'],
                'telefono' => $validated['telefono'],
                'descripcion' => $validated['descripcion'] ?? null,
                'costo' => $validated['costo'],
                'tipo_pago' => $validated['tipo_pago'],
                'admin_id' => $adminId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            return response()->json([
                'message' => 'Venta registrada con éxito'
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (Exception $e) {
            Log::error('Error al registrar venta: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Ocurrió un error inesperado. Revisa los logs para más detalles.'
            ], 500);
        }
    }

public function destroy($id)
{
    $venta = Ventas::findOrFail($id);
    $venta->delete();

    return response()->json(['message' => 'Venta eliminada correctamente.']);
}


public function update(Request $request, $id)
{
    $request->validate([
        'fecha' => 'required|date',
        'tipo_pago' => 'required|string',
        'nombre' => 'required|string|max:255',
        'telefono' => 'required|string|max:20',
        'descripcion' => 'nullable|string',
        'costo' => 'required|numeric',
    ]);

    $venta = Ventas::findOrFail($id);
    $venta->update($request->only(['fecha', 'tipo_pago', 'nombre', 'telefono', 'descripcion', 'costo']));

    return response()->json(['message' => 'Venta actualizada correctamente']);
}



}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Ventas extends Model
{
    use HasFactory;

    protected $table = 'ventas';

    protected $fillable = [
        'fecha',
        'nombre',
        'telefono',
        'descripcion',
        'costo',
        'tipo_pago',
        'admin_id',
    ];

    /**
     * Relación: una venta pertenece a un admin.
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($venta) {
            $fecha = $venta->fecha ? Carbon::parse($venta->fecha) : now();

            $venta->mes = $fecha->month;
            $venta->anio = $fecha->year;
        });
    }
}

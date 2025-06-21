<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = ['tipo', 'mesa_id', 'cliente_id', 'estado', 'total'];

    public function mesa()
    {
        return $this->belongsTo(Mesa::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetallePedido::class);
    }
}

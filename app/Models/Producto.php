<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Compra;
//use App\Models\Producto;

class Producto extends Model
{
    use HasFactory;
    protected $fillable=[
        'nombre', 'descripcion', 'precio', 'cantidad_disponible', 'categoria_id', 'marca_id'
    ];

    public function categoria(){
        return $this->belongsTo(Categoria::class);
    }

    public function marca(){
        return $this->belongsTo(Marca::class);
    }

    public function compras() //un producto puede pertenecer a varias instancias de compra y viceversa
    {
        return $this->belongsToMany(Compra::class)->withPivot('precio', 'cantidad', 'subtotal')->withTimestamps();
    }
}

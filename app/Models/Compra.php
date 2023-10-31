<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use App\Models\Compra;
use App\Models\Producto;

class Compra extends Model
{
    use HasFactory;

    protected $fillable=[
        'subtotal', 'total'
    ];

    public function productos() //una compra puede pertenecer a varias instancias de producto y viceverza
    {
        return $this->belongsToMany(Producto::class)->withPivot('precio', 'cantidad','subtotal')->withTimestamps();
    }
}

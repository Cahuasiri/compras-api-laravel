<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use App\Models\Compra;
use App\Models\Producto;
use Exception;
use App\Http\Responses\ApiResponse;

class CompraController extends Controller
{
    public function index(){
        try{
            //$compras = Compra::all();
            $compras = Compra::with('productos')->get();
            return ApiResponse::success('Lista de Compras', 200, $compras);
        }catch(Exception $e){
            return ApiResponse::error('Error al obtener Compras:'.$e->getMessage(), 500);
        }
    }

    public function store(Request $request){
        try{
            $productos = $request->input('productos');
            //validar los productos
            if(empty($productos)){
                return ApiResponse::error('No se proporcionaron productos', 400);
            }

            //Validar la lista de productos.
            $validator = Validator::make($request->all(), [
                'productos' => 'required|array',
                'productos.*.producto_id'=> 'required|integer|exists:productos,id',
                'productos.*.cantidad' => 'required|integer|min:1'
            ]);

            if($validator->fails()){
                return ApiResponse::error('Datos invalidos en la lista de productos', 400, $validator->errors());
            }
            //Validar productos duplicados
            $productoIds = array_column($productos, 'producto_id');
            if(count($productoIds)!== count(array_unique($productoIds))){
                return ApiResponse::error('No se permiten productos duplicados para la compra',400);
            }

            $totalPagar=0;
            $subtotal = 0;
            $compraItems = [];

            //Iteracion de los productos para calcular el total a pagar
            foreach($productos as $producto){
                $productoB = Producto::find($producto['producto_id']);
                if(!$productoB){
                    return ApiResponse::error('Producto no encontrado', 404);
                }

                //validar la cantidad disponible de los productos
                if($productoB->cantidad_disponible < $producto['cantidad']){
                    return ApiResponse::error('El producto no tiene cantidad suficiente disponible', 404);
                }

                //Actualizacion de la cantidad dispobible de cada producto
                $productoB->cantidad_disponible= $productoB->cantidad_disponible - $producto['cantidad'];
                $productoB->save();

                //calculo de importes
                $subtotal = $productoB->precio * $producto['cantidad'];
                $totalPagar = $totalPagar+$subtotal;

                //Items de la compra
                $compraItems[]= [
                    'producto_id' => $productoB->id,
                    'precio' => $productoB->precio,
                    'cantidad'=> $producto['cantidad'],
                    'subtotal'=> $subtotal
                ];
            }

            //Registro en al tabla compras
            $compra = Compra::create([
                'subtotal' => $totalPagar,
                'total' => $totalPagar
            ]);

            //Asociar los productos a la compra con sus cantidades y sus subtotales
            $compra->productos()->attach($compraItems);
            return ApiResponse::success('Compra Realizada Exitosamente', 201, $compra);

        }catch(QueryException $e){
            //error de consulta a la base de datos
            return ApiResponse::error('Error en la consulta de base de datos ',500);
        }catch(Exception $e){
            return ApiResponse::error('Error inesperado', 500);
        }
    }

    public function show($id){

    }
}

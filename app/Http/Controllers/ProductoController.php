<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Producto;
use Exception;
use App\Http\Responses\ApiResponse;

class ProductoController extends Controller
{
    public function index()
    {
        try{
            $productos = Producto::with('marca','categoria')->get(); // no conviene cuando los datos son miles
            //$productos = Producto::all();
            return ApiResponse::success('Lista de Productos',200, $productos);      
        } catch(Exception $e) {
           return ApiResponse::error('Error al obtener Productos:'.$e->getMessage(), 500);
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre'=> 'required|unique:productos',
                'precio'=> 'required|numeric|between:0,999999.99',
                'cantidad_disponible' => 'required|integer',
                'categoria_id'=>'required|exists:categorias,id',
                'marca_id' => 'required|exists:marcas,id'
            ]);
            $producto = Producto::create($request->all());
            return ApiResponse::success('Producto Creado Exitosamente', 200, $producto);            
        } catch(ValidationException $e){
           // $errors = $e->validator->errors()->toArray();

            if(isset($errors['categoria_id'])){
                $errors['categoria'] = $errors['categoria_id'];
                unset($errors['categoria_id']);
            }
            return ApiResponse::errorValidate($e->validator->errors(),422);
           // return ApiResponse::error('Error de Validacion: ',422, $errors);
        }
    }

    public function show(string $id)
    {
        try{
            $producto = Producto::with('marca','categoria')->findOrFail($id);
            return ApiResponse::success('Producto obtenido exitosamente',200, $producto);
        }catch(ModelNotFoundException $e){
            return ApiResponse::error('Producto no Encontrado', 404);
        } 
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        try{
            $producto = Producto::findOrFail($id);
            $request->validate([
                'nombre'=>['required', Rule::unique('productos')->ignore($producto)],
                'precio'=> 'required|numeric|between:0,999999.99',
                'cantidad_disponible' => 'required|integer',
                'categoria_id'=>'required|exists:categorias,id',
                'marca_id' => 'required|exists:marcas,id'
            ]);
            $producto->update($request->all());
            return ApiResponse::success('Producto Actualizado Exitosamente', 201, $producto);
        }catch(ModelNotFoundException $e){
            return ApiResponse::error('Producto no Encontrado', 404);
        }catch(Exception $e){
            return ApiResponse::error('Error: '.$e->getMessage(),422);
        }
    }

    public function destroy(string $id)
    {
        try{
            $producto = Producto::findOrFail($id);
            $producto->delete();
            return ApiResponse::success('Producto Eliminado Exitosamente', 200);
        }catch(ModelNotFoundException $e){
            //console.log();
            return ApiResponse::error('Producto no Encontrado', 404);
        }
    }
}

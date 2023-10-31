<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Marca;
use Exception;
use App\Http\Responses\ApiResponse;

class MarcaController extends Controller
{
    public function index()
    {
        try{
            $marcas = Marca::all();
            return ApiResponse::success('Lista de Marcas',200, $marcas);
           //throw new Exception("Error al obtener Usuarios");
        } catch(Exception $e) {
           return ApiResponse::error('Error al obtener Marcas:'.$e->getMessage(), 500);
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        try{
            $request->validate([
                'nombre'=>'required|unique:marcas',
            ]);
            $marca = Marca::create($request->all());
            return ApiResponse::success('Marca creada Exitosamente', 201, $marca);
        } catch(ValidationException $e){
            return ApiResponse::errorValidate($e->validator->errors(),422);
            //return ApiResponse::error('Error de Validacion: '.$e->getMessage(),422);
        }
    }

    public function show(string $id)
    {
        try{
            $marca = Marca::findOrFail($id);
            return ApiResponse::success('Marca obtenido exitosamente',200, $marca);
        }catch(ModelNotFoundException $e){
            return ApiResponse::error('Marca no Encontrado', 404);
        } 
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        try{
            $marca = Marca::findOrFail($id);
            $request->validate([
                'nombre'=>['required', Rule::unique('marcas')->ignore($marca)],
            ]);
            $marca->update($request->all());
            return ApiResponse::success('Marca Actualizado Exitosamente', 201, $marca);
        }catch(ModelNotFoundException $e){
            return ApiResponse::error('Marca no Encontrado', 404);
        }catch(Exception $e){
            //return ApiResponse::error('Error: '.$e->getMessage(),422);
            return ApiResponse::errorValidate($e->validator->errors(),422);
        }
    }

    public function destroy(string $id)
    {
        try{
            $marca = Marca::findOrFail($id);
            $marca->delete();
            return ApiResponse::success('Marca Eliminado Exitosamente', 200);
        }catch(ModelNotFoundException $e){
            return ApiResponse::error('Marca no Encontrado', 404);
        }
    }

    public function productosPorMarca($id){
        try{
            $marca = Marca::with('productos')->findOrFail($id);
            return ApiResponse::success('Marca y lista de productos', 200, $marca);
        }catch(ModelNotFoundException $e){
            return ApiResponse::error('Marca no Encontrado', 404);
        }
    }
}

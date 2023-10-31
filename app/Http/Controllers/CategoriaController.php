<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Categoria;
use Exception;
use App\Http\Responses\ApiResponse;

class CategoriaController extends Controller
{

    public function index()
    {
        try{
            $categorias = Categoria::all();
            return ApiResponse::success('Lista de Categorias',200, $categorias);
           //throw new Exception("Error al obtener Usuarios");
        } catch(Exception $e) {
           return ApiResponse::error('Error al obtener Categorias:'.$e->getMessage(), 500);
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {   
        // $validator = Validator::make($request->all(),[
        //     'nombre'=>'required|unique:categorias',
        //     'descripcion'=>'required',
        // ]);

        // if($validator->fails()){
        //     return response()->json([
        //         'status'=>422,
        //         'errors'=>$validator->messages()
        //     ]);
        // }else{
            
        // }
         try{
              $request->validate([
                  'nombre'=>'required|unique:categorias',
                  'descripcion'=>'required',
              ]);
            //  $validator = Validator::make($request->all(), [
            //     'nombre'=>'required|unique:categorias',
            //     'descripcion'=>'required'
            //  ]);
             $categoria = Categoria::create($request->all());
             return ApiResponse::success('Categoria creada Exitosamente', 201, $categoria);
         } catch(ValidationException $e){            
             return ApiResponse::errorValidate($e->validator->errors(),422);
             //return ApiResponse::error('Error de Validacion: '.$e->getMessage(),422);
            //  return response()->json([
            //     'status'=>422,                
            //     'error'=>$e->validator->errors()
            // ]);
         }
    }

    public function show(string $id)
    {
        try{
            $categoria = Categoria::findOrFail($id);
            return ApiResponse::success('Categoria obtenido exitosamente',200, $categoria);
        }catch(ModelNotFoundException $e){
            return ApiResponse::error('Categoria no Encontrado', 404);
        } 
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        try{
            $categoria = Categoria::findOrFail($id);
            $request->validate([
                'nombre'=>['required', Rule::unique('categorias')->ignore($categoria)],
            ]);
            $categoria->update($request->all());
            return ApiResponse::success('Categoria Actualizado Exitosamente', 201, $categoria);
        }catch(ModelNotFoundException $e){
            return ApiResponse::error('Categoria no Encontrado', 404);
        }catch(Exception $e){
            return ApiResponse::errorValidate($e->validator->errors(),422);
        }
    }

    public function destroy(string $id)
    {
        try{
            $categoria = Categoria::findOrFail($id);
            $categoria->delete();
            return ApiResponse::success('Categoria Eliminado Exitosamente', 200);
        }catch(ModelNotFoundException $e){
            return ApiResponse::error('Categoria no Encontrado', 404);
        }
    }

    public function productosPorCategoria($id){

        try{
            $categoria = Categoria::with('productos')->findOrFail($id);
            return ApiResponse::success('Categoria y lista de productos', 200, $categoria);
        }catch(ModelNotFoundException $e){
            return ApiResponse::error('Categoria no Encontrado', 404);
        }
        
    }
}

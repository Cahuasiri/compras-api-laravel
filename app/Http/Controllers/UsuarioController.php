<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\Usuario;
use Exception;
use App\Http\Responses\ApiResponse;

class UsuarioController extends Controller
{
    public function index()
    {
        try{
             $usuarios = Usuario::all();
             return ApiResponse::success('Lista de Usuarios',200, $usuarios);
            //throw new Exception("Error al obtener Usuarios");
        } catch(Exception $e) {

            return ApiResponse::error('Error al obtener Usuarios:'.$e->getMessage(), 500);
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
                'usuario'=>'required|unique:usuarios',
                'email'=>'required',
                'password'=>'required'
            ]);
            $usuario = Usuario::create($request->all());
            return ApiResponse::success('Categoria creada Exitosamente', 201, $usuario);
        } catch(ValidationException $e){
            return ApiResponse::error('Error de Validacion: '.$e->getMessage(),422);
        }
    }
    public function show(string $id)
    {   
        try{
            $usuario = Usuario::findOrFail($id);
            return ApiResponse::success('Usuario obtenido exitosamente',200, $usuario);
        }catch(ModelNotFoundException $e){
            return ApiResponse::error('Usuario no Encontrado', 404);
        }     
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        try{
            $usuario = Usuario::findOrFail($id);
            $request->validate([
                'usuario'=>['required', Rule::unique('usuarios')->ignore($usuario)],
                'email'=>'required',
                'password'=>'required'
            ]);
            $usuario->update($request->all());
            return ApiResponse::success('Usuario Actualizado Exitosamente', 201, $usuario);
        }catch(ModelNotFoundException $e){
            return ApiResponse::error('Usuario no Encontrado', 404);
        }catch(Exception $e){
            return ApiResponse::error('Error: '.$e->getMessage(),422);
        }
    }

    public function destroy(string $id)
    {   
        try{
            $usuario = Usuario::findOrFail($id);
            $usuario->delete();
            return ApiResponse::success('Usuario Eliminado Exitosamente', 200);
        }catch(ModelNotFoundException $e){
            return ApiResponse::error('Usuario no Encontrado', 404);
        }
       
    }
}

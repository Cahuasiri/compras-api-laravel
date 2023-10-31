<?php

namespace App\Http\Responses;
class ApiResponse
{
    public static function success($message = "Success", $status=200, $data=[])
    {
        return response()->json([
            'message'=>$message,
            'status'=>$status,
            'error'=>false,
            'data'=>$data
        ], $status);
    }

    public static function error($message="Error", $status, $data=[])
    {
        return response()->json([
            'message'=>$message,
            'status'=>$status,
            'error'=>true,
            'data'=>$data
        ], $status);
    }

    public static function errorValidate($messages=[], $status){
        return response()->json([
            'status'=>$status,
            'error'=>true,
            'messages'=>$messages,
        ], $status);
    }
}
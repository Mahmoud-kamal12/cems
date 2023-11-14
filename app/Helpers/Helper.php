<?php

namespace App\Helpers;

class Helper
{
    public static function ResponseSuccuss($data = [], string $msg = "Successfully", bool $error = false, int $status = 200): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            "error" => $error,
            "msg" => $msg,
            "data" => $data
        ] , $status);
    }

    public static function ResponseError(bool $error = true , string $msg = "Error", $data = [] , int $status = 500): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            "error" => $error,
            "msg" => $msg,
            "data" => $data
        ] , $status);
    }
}

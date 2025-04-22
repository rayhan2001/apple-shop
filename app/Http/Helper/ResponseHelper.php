<?php

namespace App\Http\Helper;
use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    public static function Out($msg, $data, $code): JsonResponse
    {
        return response()->json([
            'message' => $msg,
            'data' => $data,
        ], $code);
    }
}
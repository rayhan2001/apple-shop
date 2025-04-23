<?php

namespace App\Helper;

class ResponseHelper
{
    public static function Out($msg, $data, $code)
    {
        return response()->json([
            'message' => $msg,
            'data' => $data,
        ], $code);
    }
}
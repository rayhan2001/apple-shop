<?php

namespace App\Helper;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JWTToken
{
    public static function createToken($userEmail, $userId): string
    {
        $key = env('JWT_KEY');
        $payload = [
            'iss' => 'laravel-jwt', // Issuer of the token
            'iat' => time(), // Issued at: time when the token was generated
            'exp' => time() + (60 * 60), // Expiration time: 1 hour from now
            'userEmail' => $userEmail, // User email
            'userId' => $userId, // User ID
        ];

        return JWT::encode($payload, $key, 'HS256');
    }

    public static function readToken($token)
    {
        try {
            if($token == null) {
                return 'Unauthorized';
            }else{
                $key = env('JWT_KEY');
                return JWT::decode($token, new Key($key, 'HS256'));
            }
        } catch (Exception $e) {
            return 'Unauthorized'; // Token is invalid or expired
        }
    }

}
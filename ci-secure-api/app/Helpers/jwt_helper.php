<?php

use Config\Services;
use Firebase\JWT\JWT;
use App\Models\ModeloUsuario;
use CodeIgniter\Config\Services as ConfigServices;
use Firebase\JWT\Key;

//Obtener autentificacion, y devuelve el token
function getJWTFromRequest($autentificacionHeader): string
{
    if (is_null($autentificacionHeader)) {
        throw new Exception('Faltante o invalido JWT en la solicitud');
    }

    return explode(' ', $autentificacionHeader)[1];
}

//toma el token obtenido y lo valida con nuestro registro en nuestra base de datos
function validateJWTFromRequest(string $encodedToken)
{
    $key = Services::getSecretKey();
    $decodedToken = JWT::decode($encodedToken, new Key($key, 'HS256'));
    $modeloUsuario = new ModeloUsuario();
    $modeloUsuario->buscarUsuarioPorEmail($decodedToken->email);
}

//Obtener un token firmado
function getSignedJWTForUser(string $email): string
{
    $issuedAtTime = time();
    $tokenTimeToLive = getenv('JWT_TIME_TO_LIVE');
    $tokenExpiration = $issuedAtTime + $tokenTimeToLive;
    $payload = [
        'email' => $email,
        'iat' => $issuedAtTime,
        'exp' => $tokenExpiration
    ];

    $jwt = \Firebase\JWT\JWT::encode($payload, \Config\Services::getSecretKey(), 'HS256');// Solicita tres argumentos

    return $jwt;
}
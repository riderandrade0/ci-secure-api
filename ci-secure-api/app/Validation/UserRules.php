<?php

namespace App\Validation;

use App\Models\ModeloUsuario;

class UserRules
{
    public function validarUsuario(string $str, string $fields, array $data):bool{
        try {
            $modelo = new ModeloUsuario();
            $usuario = $modelo->buscarUsuarioPorEmail($data['email']);
            return password_verify($data['password'], $usuario['password']);
        } catch (\Exception $e) {
            return false;
        }

    }
}

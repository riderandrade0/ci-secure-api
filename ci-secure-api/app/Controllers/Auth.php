<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\ModeloUsuario;

class Auth extends BaseController
{
    public function registrar()
    {
        //\
        //echo 'entra a auth registrar';
        $rules = [
            'name' => 'required',
            'email' => 'required|valid_email|is_unique[usuario.email]',
            'password' => 'required|min_length[8]|max_length[255]'
        ];
        
        $input = $this->getRequestInput($this->request);
        if (!$this->validarPedido($input, $rules)) {
            return $this->getResponse($this->validator->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
        }
        //var_dump($input);

        $usuarioModelo = new ModeloUsuario();
        $usuarioModelo->save($input);

        return $this->getJWTParaUsuario($input['email'], ResponseInterface::HTTP_CREATED);
    }

    public function login(){
        $rules = [
            'email' => 'required|min_length[6]|max_length[60]|valid_email',
            'password' => 'required|min_length[8]|max_length[255]|validarUsuario[email, password]'
        ];

        $errors = [
            'password' => [
                'validarUsuario' => 'Credencial proveido invalido para login'
            ]
        ];

        $input = $this->getRequestInput($this->request);

        if (!$this->validarPedido($input, $rules, $errors)) {
            return $this->getResponse($this->validator->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
        }
        return $this->getJWTParaUsuario($input['email']);
    }

    private function getJWTParaUsuario(string $email, int $responseCode = ResponseInterface::HTTP_OK){
        try {
            $modelo = new ModeloUsuario();
            $usuario = $modelo->buscarUsuarioPorEmail($email);
            unset($usuario['password']);

            helper('jwt');

            return $this->getResponse([
                'message' => 'Usuario autentificado correctamente',
                'usuario' => $usuario,
                'access_token' => getSignedJWTForUser($email)
            ]);
        } catch (\Exception $e) {
            return $this->getResponse([
                'error' => $e->getMessage()
            ], $responseCode);
        }
    }
}

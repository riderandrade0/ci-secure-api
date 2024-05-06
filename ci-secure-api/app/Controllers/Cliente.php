<?php

namespace App\Controllers;

use Exception;
use App\Models\ModeloCliente;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Cliente extends BaseController
{
    public function index()
    {
        //echo 'index cliente.php';
        $model = new ModeloCliente();
        return $this->getResponse([
            'message' => 'Cliente obtenido correctamente',
            'clients' => $model->findAll()
        ]);
    }

    public function store()
    {
        //validacion de todos los campos enviados via post
        echo ('Entra a añadir cliente');
        $rules = [
            'name' => 'required',
            'email' => 'required|min_length[6]|max_length[50]|valid_email|is_unique[cliente.email]',
            'retainer_fee' => 'required|max_length[255]'
        ];

        $input = $this->getRequestInput($this->request);

        if (!$this->validarPedido($input, $rules)) {
            return $this->getResponse($this->validator->getErrors(), ResponseInterface::HTTP_BAD_REQUEST);
        }

        $clientEmail = $input['email'];

        $model = new ModeloCliente();
        $model->save($input);


        $client = $model->where('email', $clientEmail)->first();

        return $this->getResponse([
            'message' => 'Cliente añadido correctamente',
            'client' => $client
        ]);
    }

    public function show($id)
    {
        echo ('entra a by id');
        try {

            $model = new ModeloCliente();
            $client = $model->buscarClienteById($id);

            return $this->getResponse([
                'message' => 'Cliente encontrado',
                'cliente' => $client
            ]);

        } catch (Exception $e) {
            return $this->getResponse([
                'message' => 'Could not find client for specified ID'
            ], ResponseInterface::HTTP_NOT_FOUND);
        }
    }

    public function update($id)
    {
        try {

            $model = new ModeloCliente();
            $model->buscarClienteById($id);

            $input = $this->getRequestInput($this->request);


            $model->update($id, $input);
            $client = $model->buscarClienteById($id);

            return $this->getResponse([
                'message' => 'Client updated successfully',
                'client' => $client
            ]);

        } catch (Exception $exception) {

            return $this->getResponse([
                'message' => $exception->getMessage()
            ], ResponseInterface::HTTP_NOT_FOUND);
        }
    }

    public function destroy($id)
    {
        try {

            $model = new ModeloCliente();
            $client = $model->buscarClienteById($id);
            $model->delete($client);

            return $this->getResponse([
                'message' => 'Client deleted successfully',
            ]);

        } catch (Exception $exception) {
            return $this->getResponse([
                'message' => $exception->getMessage()
            ], ResponseInterface::HTTP_NOT_FOUND);
        }
    }
}

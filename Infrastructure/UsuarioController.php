<?php
namespace Infrastructure;

class UsuarioController {

    private $gestionUsuarios;

    public function __construct($gestionUsuarios) {
        $this->gestionUsuarios = $gestionUsuarios;
    }

    public function crear($data) {

        return $this->gestionUsuarios->crear(
            $data['nombre'],
            $data['email'],
            $data['telefono'],
            $data['password'],
            $data['rol']
        );
    }

    public function listar() {
        return $this->gestionUsuarios->listar();
    }

    public function eliminar($id) {
        return $this->gestionUsuarios->eliminar($id);
    }

    public function buscarPorId($id) {
        return $this->gestionUsuarios->buscarPorId($id);
    }
}
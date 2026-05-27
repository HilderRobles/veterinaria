<?php
namespace Application;

use Domain\Usuario;
use Domain\GestionUsuarios;
use Domain\RepositorioUsuarios;

class GestionUsuariosImpl implements GestionUsuarios {

    private $repositorio;

    public function __construct(
        RepositorioUsuarios $repositorio
    ) {
        $this->repositorio = $repositorio;
    }

    public function crear(
        $nombre,
        $email,
        $telefono,
        $password,
        $rol
    ) {

        $usuario = new Usuario(
            $nombre,
            $email,
            $telefono,
            password_hash($password, PASSWORD_BCRYPT),
            $rol
        );

        return $this->repositorio->guardar($usuario);
    }

    public function listar() {
        return $this->repositorio->listar();
    }

    public function eliminar($id) {
        return $this->repositorio->eliminar($id);
    }

    public function buscarPorId($id) {
        return $this->repositorio->buscarPorId($id);
    }
}
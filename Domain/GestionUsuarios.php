<?php
namespace Domain;

interface GestionUsuarios {
    public function crear(
        $nombre,
        $email,
        $telefono,
        $password,
        $rol
    );

    public function listar();

    public function eliminar($id);

    public function buscarPorId($id);
}
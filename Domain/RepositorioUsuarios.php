<?php
namespace Domain;

interface RepositorioUsuarios {
    public function guardar(Usuario $usuario);

    public function buscarPorId($id);

    public function eliminar($id);

    public function listar();
}
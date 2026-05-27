<?php
namespace Domain;

interface RepositorioMascotas {

    public function guardar(Mascota $mascota);

    public function buscarPorId($id);

    public function listar();

    public function eliminar($id);
}
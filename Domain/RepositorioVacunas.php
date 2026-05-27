<?php
namespace Domain;

interface RepositorioVacunas {

    public function guardar(Vacuna $vacuna);

    public function actualizar(Vacuna $vacuna);

    public function buscarPorId($id);

    public function listar();
}
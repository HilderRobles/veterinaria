<?php
namespace Domain;

interface RepositorioHistorialClinico {

    public function guardar(
        HistorialClinico $historial
    );

    public function listar();

    public function buscarPorMascota($idMascota);
}
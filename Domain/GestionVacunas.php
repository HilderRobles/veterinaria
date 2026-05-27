<?php
namespace Domain;

interface GestionVacunas {

    public function crear(
        $nombre,
        $stock,
        $precio
    );

    public function listar();

    public function aplicar($id);
}
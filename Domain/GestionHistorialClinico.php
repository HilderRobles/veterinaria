<?php
namespace Domain;

interface GestionHistorialClinico {

    public function crear(
        $idMascota,
        $idCita,
        $diagnostico,
        $observaciones
    );

    public function listar();

    public function buscarPorMascota($idMascota);
}
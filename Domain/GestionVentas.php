<?php
namespace Domain;

interface GestionVentas {

    public function registrar(
        $idUsuario,
        $montoTotal
    );

    public function listar();

    public function buscarPorId($id);
}
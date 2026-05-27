<?php
namespace Domain;

interface GestionMascotas {

    public function crear(
        $idUsuario,
        $nombre,
        $especie,
        $raza,
        $edad,
        $peso,
        $sexo
    );

    public function listar();

    public function buscarPorId($id);

    public function eliminar($id);
}
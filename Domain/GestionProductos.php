<?php
namespace Domain;

interface GestionProductos {

    public function crear(
        $nombre,
        $descripcion,
        $precio,
        $stock,
        $categoria
    );

    public function listar();

    public function actualizarStock($id, $cantidad);
}
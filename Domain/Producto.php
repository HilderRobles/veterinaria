<?php
namespace Domain;

class Producto {

    private $id;
    private $nombre;
    private $descripcion;
    private $precio;
    private $stock;
    private $categoria;

    public function __construct(
        $nombre,
        $descripcion,
        $precio,
        $stock,
        $categoria,
        $id = null
    ) {

        if ($precio < 0) {
            throw new \Exception("Precio inválido");
        }

        if ($stock < 0) {
            throw new \Exception("Stock inválido");
        }

        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->precio = $precio;
        $this->stock = $stock;
        $this->categoria = $categoria;
        $this->id = $id;
    }

    public function reducirStock($cantidad) {

        if ($cantidad > $this->stock) {
            throw new \Exception("Stock insuficiente");
        }

        $this->stock -= $cantidad;
    }
}
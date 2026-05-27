<?php
namespace Domain;

class Vacuna {

    private $id;
    private $nombre;
    private $stock;
    private $precio;

    public function __construct(
        $nombre,
        $stock,
        $precio,
        $id = null
    ) {

        if ($stock < 0) {
            throw new \Exception("Stock inválido");
        }

        $this->nombre = $nombre;
        $this->stock = $stock;
        $this->precio = $precio;
        $this->id = $id;
    }

    public function aplicarDosis() {

        if ($this->stock <= 0) {
            throw new \Exception("No hay vacunas disponibles");
        }

        $this->stock--;
    }
}
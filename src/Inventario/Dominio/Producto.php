<?php

namespace App\Inventario\Dominio;

use App\Inventario\Dominio\ObjetoValor\Precio;

class Producto {
    public function __construct(
        private string $id,
        private string $nombre,
        private string $descripcion,
        private Precio $precio
    ) {}

    // El sistema debe permitir modificar la descripción
    public function cambiarDescripcion(string $nuevaDescripcion): void {
        $this->descripcion = $nuevaDescripcion;
    }

    // El sistema debe permitir modificar el precio
    public function cambiarPrecio(Precio $nuevoPrecio): void {
        $this->precio = $nuevoPrecio;
    }

    // Getters para poder leer los datos desde fuera
    public function id(): string { return $this->id; }
    public function nombre(): string { return $this->nombre; }
    public function descripcion(): string { return $this->descripcion; }
    public function precio(): float { return $this->precio->valor(); }
}
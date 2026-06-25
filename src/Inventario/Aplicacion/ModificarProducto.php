<?php

namespace App\Inventario\Application;

use App\Inventario\Dominio\RepositorioProducto;
use App\Inventario\Dominio\ObjetoValor\Precio;

class ModificarProducto {
    public function __construct(
        private RepositorioProducto $repositorio
    ) {}

    public function ejecutar(string $id, string $nuevaDescripcion, float $nuevoPrecioValor): void {
        // 1. Buscamos que el producto exista
        $producto = $this->repositorio->buscarPorId($id);
        if ($producto === null) {
            throw new \DomainException("El producto no existe.");
        }

        // 2. Creamos el objeto de valor Precio (si es <= 0, aquí saltará el error automáticamente)
        $nuevoPrecio = new Precio($nuevoPrecioValor);

        // 3. Modificamos la entidad con sus métodos seguros de Dominio
        $producto->cambiarDescripcion($nuevaDescripcion);
        $producto->cambiarPrecio($nuevoPrecio);

        // 4. Guardamos el producto actualizado en el repositorio
        $this->repositorio->guardar($producto);
    }
}
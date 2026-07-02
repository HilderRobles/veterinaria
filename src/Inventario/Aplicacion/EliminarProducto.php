<?php

namespace App\Inventario\Aplicacion;

use App\Inventario\Dominio\RepositorioProducto;

class EliminarProducto {
    public function __construct(private RepositorioProducto $repositorio) {}

    public function ejecutar(string $id): void {
        // Buscamos si el producto existe antes de borrarlo
        $producto = $this->repositorio->buscarPorId($id);
        
        if ($producto === null) {
            throw new \DomainException("No se puede eliminar un producto que no existe.");
        }

        // Si existe, le ordenamos al repositorio que lo borre
        $this->repositorio->eliminar($id);
    }
}
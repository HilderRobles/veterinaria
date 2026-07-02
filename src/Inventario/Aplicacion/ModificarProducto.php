<?php

namespace App\Inventario\Aplicacion;

use App\Inventario\Dominio\Producto;
use App\Inventario\Dominio\RepositorioProducto;
use App\Inventario\Dominio\ObjetoValor\Precio;

class ModificarProducto {
    public function __construct(
        private RepositorioProducto $repositorio
    ) {}

    public function ejecutar(string $id, string $nuevoNombre, float $nuevoPrecioValor): void {
        // 1. Buscamos que el producto exista
        $productoExistente = $this->repositorio->buscarPorId($id);
        if ($productoExistente === null) {
            throw new \DomainException("El producto no existe.");
        }

        // 2. Creamos el objeto de valor Precio revisando que sea correcto
        $nuevoPrecio = new Precio($nuevoPrecioValor);

        // 3. SOLUCIÓN: Creamos una nueva instancia del producto con los datos actualizados
        // Pasamos el ID original, el nuevo nombre, la descripción existente, y el nuevo precio.
        $productoModificado = new Producto(
            $id, 
            $nuevoNombre, 
            $productoExistente->descripcion(), // Mantiene la descripción que ya tenía
            $nuevoPrecio
        );

        // 4. Guardamos el producto actualizado reemplazando el anterior
        $this->repositorio->guardar($productoModificado);
    }
}
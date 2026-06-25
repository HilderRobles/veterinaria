<?php

namespace App\Inventario\Aplicacion;

use App\Inventario\Dominio\Producto;
use App\Inventario\Dominio\RepositorioProducto;
use App\Inventario\Dominio\ObjetoValor\Precio;

class CrearProducto {
    // 1. Pedimos las herramientas necesarias (Inyección de Dependencias)
    public function __construct(
        private RepositorioProducto $repositorio
    ) {}

    public function ejecutar(string $id, string $nombre, string $descripcion, float $precioValor): void {
        // 2. Regla de Negocio: Validar que el producto no exista previamente
        if ($this->repositorio->buscarPorId($id) !== null) {
            throw new \DomainException("El producto con ID '{$id}' ya se encuentra registrado en el inventario.");
        }

        // 3. Encapsulamos el precio en su Objeto de Valor. 
        // Si el precio es 0 o negativo, aquí saltará el error de "Precio.php" automáticamente.
        $precio = new Precio($precioValor);

        // 4. Creamos la entidad Producto con todos sus datos validados
        $producto = new Producto($id, $nombre, $descripcion, $precio);

        // 5. Le ordenamos al repositorio que lo guarde en el sistema
        $this->repositorio->guardar($producto);
    }
}
<?php

namespace App\Tests\Inventario\Aplicacion;

use App\Inventario\Aplicacion\EliminarProducto;
use App\Inventario\Dominio\Producto;
use App\Inventario\Dominio\RepositorioProducto;
use App\Inventario\Dominio\ObjetoValor\Precio;
use PHPUnit\Framework\TestCase;

class RepositorioFakeEliminar implements RepositorioProducto {
    private array $productos = [];
    public function guardar(Producto $producto): void { $this->productos[$producto->id()] = $producto; }
    public function buscarPorId(string $id): ?Producto { return $this->productos[$id] ?? null; }
    public function eliminar(string $id): void { unset($this->productos[$id]); }
}

/**
 * @covers \App\Inventario\Aplicacion\EliminarProducto
 */
class EliminarProductoTest extends TestCase {
    
    public function test_debe_eliminar_un_producto_existente_exitosamente(): void {
        $repositorioFalso = new RepositorioFakeEliminar();
        $producto = new Producto("PROD-888", "Fármaco", "Descripción", new Precio(45.00));
        $repositorioFalso->guardar($producto);

        $casoDeUsoEliminar = new EliminarProducto($repositorioFalso);
        $casoDeUsoEliminar->ejecutar("PROD-888");

        $productoEliminado = $repositorioFalso->buscarPorId("PROD-888");
        $this->assertNull($productoEliminado);
    }
}
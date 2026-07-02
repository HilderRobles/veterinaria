<?php

namespace App\Tests\Inventario\Aplicacion;

use App\Inventario\Aplicacion\ModificarProducto;
use App\Inventario\Dominio\Producto;
use App\Inventario\Dominio\RepositorioProducto;
use App\Inventario\Dominio\ObjetoValor\Precio;
use PHPUnit\Framework\TestCase;

class RepositorioFakeModificar implements RepositorioProducto {
    private array $productos = [];
    public function guardar(Producto $producto): void { $this->productos[$producto->id()] = $producto; }
    public function buscarPorId(string $id): ?Producto { return $this->productos[$id] ?? null; }
    public function eliminar(string $id): void {}
}

/**
 * @covers \App\Inventario\Aplicacion\ModificarProducto
 */
class ModificarProductoTest extends TestCase { 
    
    public function test_debe_modificar_un_producto_existente_exitosamente(): void {
        $repositorioFalso = new RepositorioFakeModificar();
        $productoInicial = new Producto("PROD-777", "Arena de Gato", "Arena clásica", new Precio(12.00));
        $repositorioFalso->guardar($productoInicial);

        $casoDeUsoModificar = new ModificarProducto($repositorioFalso);
        $casoDeUsoModificar->ejecutar("PROD-777", "Arena Premium", 18.50);

        $productoModificado = $repositorioFalso->buscarPorId("PROD-777");
        $this->assertEquals("Arena Premium", $productoModificado->nombre());
        $this->assertEquals(18.50, $productoModificado->precio());
    }
}
<?php

namespace App\Tests\Inventario\Dominio;

use App\Inventario\Dominio\Producto;
use App\Inventario\Dominio\ObjetoValor\Precio;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Inventario\Dominio\Producto
 */
class ProductoTest extends TestCase {

    public function test_debe_crear_un_producto_con_datos_correctos(): void {
        $precio = new Precio(10.00);
        $producto = new Producto("PROD-1", "Medicamento", "Detalle", $precio);

        $this->assertEquals("PROD-1", $producto->id());
        $this->assertEquals("Medicamento", $producto->nombre());
        $this->assertEquals(10.00, $producto->precio());
    }

    public function test_debe_permitir_cambiar_descripcion_y_precio(): void {
        $precioInicial = new Precio(10.00);
        $producto = new Producto("PROD-1", "Medicamento", "Detalle", $precioInicial);

        // Probamos los métodos de cambio del dominio
        $producto->cambiarDescripcion("Nueva Descripcion");
        $producto->cambiarPrecio(new Precio(20.00));

        $this->assertEquals("Nueva Descripcion", $producto->descripcion());
        $this->assertEquals(20.00, $producto->precio());
    }
}
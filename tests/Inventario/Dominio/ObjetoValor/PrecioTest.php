<?php

namespace App\Tests\Inventario\Dominio\ObjetoValor;

use App\Inventario\Dominio\ObjetoValor\Precio;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Inventario\Dominio\ObjetoValor\Precio
 */
class PrecioTest extends TestCase {

    public function test_debe_crear_precio_valido(): void {
        $precio = new Precio(15.50);
        $this->assertEquals(15.50, $precio->valor());
    }

    public function test_debe_lanzar_excepcion_si_precio_es_negativo(): void {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("El precio del producto debe ser mayor a cero.");

        new Precio(-1.00);
    }
}
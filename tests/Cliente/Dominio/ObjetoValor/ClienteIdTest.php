<?php

namespace Test\Cliente\Dominio\ObjetoValor;

use App\Cliente\Dominio\ObjetoValor\ClienteId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ClienteId::class)]
class ClienteIdTest extends TestCase {
    public function test_debe_crear_id_valido(): void {
        $id = new ClienteId(15);
        $this->assertEquals(15, $id->valor());
    }

    public function test_debe_lanzar_excepcion_si_el_id_es_cero(): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("El ID del cliente debe ser un número entero positivo.");
        
        new ClienteId(0);
    }

    public function test_debe_lanzar_excepcion_si_el_id_es_negativo(): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("El ID del cliente debe ser un número entero positivo.");

        new ClienteId(-5);
    }
}
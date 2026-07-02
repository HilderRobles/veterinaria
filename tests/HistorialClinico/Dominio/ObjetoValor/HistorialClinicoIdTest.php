<?php

namespace Tests\HistorialClinico\Dominio\ObjetoValor;

use App\HistorialClinico\Dominio\ObjetoValor\HistorialClinicoId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(HistorialClinicoId::class)]
class HistorialClinicoIdTest extends TestCase {
    public function test_debe_crear_id_valido(): void {
        $id = new HistorialClinicoId(10);
        $this->assertEquals(10, $id->valor());
    }

    public function test_debe_fallar_si_el_id_no_es_positivo(): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("El ID del historial clínico debe ser un número entero positivo.");

        new HistorialClinicoId(0);
    }
}

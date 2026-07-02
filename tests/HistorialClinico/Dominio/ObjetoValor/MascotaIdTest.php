<?php

namespace Tests\HistorialClinico\Dominio\ObjetoValor;

use App\HistorialClinico\Dominio\ObjetoValor\MascotaId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(MascotaId::class)]
class MascotaIdTest extends TestCase {
    public function test_debe_crear_id_de_mascota_valido(): void {
        $id = new MascotaId(7);
        $this->assertEquals(7, $id->valor());
    }

    public function test_debe_fallar_si_el_id_de_mascota_es_negativo(): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("El ID de la mascota debe ser un número entero positivo.");

        new MascotaId(-1);
    }
}

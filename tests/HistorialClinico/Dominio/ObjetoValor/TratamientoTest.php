<?php

namespace Tests\HistorialClinico\Dominio\ObjetoValor;

use App\HistorialClinico\Dominio\ObjetoValor\Tratamiento;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Tratamiento::class)]
class TratamientoTest extends TestCase {
    public function test_debe_crear_tratamiento_valido(): void {
        $tratamiento = new Tratamiento('Aplicar gotas por 7 días');
        $this->assertEquals('Aplicar gotas por 7 días', $tratamiento->valor());
    }

    public function test_debe_fallar_si_el_tratamiento_esta_vacio(): void {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("El tratamiento no puede estar vacío.");

        new Tratamiento('   ');
    }

    public function test_debe_fallar_si_el_tratamiento_es_demasiado_corto(): void {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("El tratamiento debe tener al menos 5 caracteres.");

        new Tratamiento('Nada');
    }
}

<?php

namespace Tests\HistorialClinico\Dominio\ObjetoValor;

use App\HistorialClinico\Dominio\ObjetoValor\Diagnostico;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Diagnostico::class)]
class DiagnosticoTest extends TestCase {
    public function test_debe_crear_diagnostico_valido(): void {
        $diagnostico = new Diagnostico('Otitis leve');
        $this->assertEquals('Otitis leve', $diagnostico->valor());
    }

    public function test_debe_fallar_si_el_diagnostico_esta_vacio(): void {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("El diagnóstico no puede estar vacío.");

        new Diagnostico('   ');
    }

    public function test_debe_fallar_si_el_diagnostico_es_demasiado_corto(): void {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("El diagnóstico debe tener al menos 5 caracteres.");

        new Diagnostico('Leve');
    }
}

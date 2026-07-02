<?php

namespace Test\Cliente\Dominio\ObjetoValor;

use App\Cliente\Dominio\ObjetoValor\CorreoElectronico;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CorreoElectronico::class)]
class CorreoElectronicoTest extends TestCase {
    public function test_debe_crear_correo_valido_y_normalizarlo(): void {
        $correo = new CorreoElectronico("  VET@ejemplo.com  ");
        $this->assertEquals("vet@ejemplo.com", $correo->valor());
    }

    public function test_debe_lanzar_excepcion_si_el_formato_es_invalido(): void {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("El formato del correo electrónico no es válido.");

        new CorreoElectronico("correo-invalido.com");
    }
    public function testDeberiaDetectarSiSonDiferentes(): void
    {
        $correoA = new CorreoElectronico("gon@gmail.com");
        $correoB = new CorreoElectronico("pedro@outlook.com");

        $this->assertFalse($correoA->esIgualA($correoB));
    }
}
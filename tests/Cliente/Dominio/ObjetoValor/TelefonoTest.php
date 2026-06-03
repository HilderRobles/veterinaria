<?php

namespace Tests\Cliente\Dominio\ObjetoValor;

use App\Cliente\Dominio\ObjetoValor\Telefono;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Telefono::class)]
class TelefonoTest extends TestCase {
    public function test_debe_crear_telefono_valido(): void {
        $telefono = new Telefono("1234567890");
        $this->assertEquals("1234567890", $telefono->valor());
    }

    public function test_debe_fallar_si_tiene_letras(): void {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("El teléfono debe contener exactamente 10 dígitos numéricos.");

        new Telefono("12345s7890");
    }

    public function test_debe_fallar_si_no_tiene_diez_digitos(): void {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("El teléfono debe contener exactamente 10 dígitos numéricos.");
        
        new Telefono("12345");
    }
}
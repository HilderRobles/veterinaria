<?php

namespace Tests\Cliente\Dominio\ObjetoValor;

use App\Cliente\Dominio\ObjetoValor\Contrasena;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Contrasena::class)]
class ContrasenaTest extends TestCase {
    public function test_debe_crear_contrasena_valida_y_permitir_verificarla(): void {
        $clavePlana = "ClaveSecreta123";
        $hashReal = password_hash($clavePlana, PASSWORD_BCRYPT);
        
        $contrasena = new Contrasena($hashReal);
        
        $this->assertEquals($hashReal, $contrasena->valor());
        $this->assertTrue($contrasena->verificar($clavePlana));
        $this->assertFalse($contrasena->verificar("ClaveIncorrecta"));
    }

    public function test_debe_fallar_si_el_hash_de_la_contrasena_esta_vacio(): void {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("La contraseña no puede estar vacía.");
        
        new Contrasena("   ");
    }
}
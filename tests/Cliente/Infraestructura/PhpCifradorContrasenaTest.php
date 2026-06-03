<?php

namespace Tests\Cliente\Infraestructura;

use App\Cliente\Infraestructura\PhpCifradorContrasena;
use App\Cliente\Dominio\ObjetoValor\Contrasena;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(PhpCifradorContrasena::class)]
#[UsesClass(Contrasena::class)]
class PhpCifradorContrasenaTest extends TestCase {
    
    public function test_debe_cifrar_la_contrasena_y_generar_un_hash_compatible(): void {
        $cifrador = new PhpCifradorContrasena();
        $contrasenaObjeto = $cifrador->cifrar("MiClave123");

        $this->assertNotEmpty($contrasenaObjeto->valor());
        $this->assertTrue(password_verify("MiClave123", $contrasenaObjeto->valor()));
    }
}
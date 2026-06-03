<?php

namespace Tests\Cliente\Dominio\ObjetoValor;

use App\Cliente\Dominio\ObjetoValor\Telefono;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Telefono::class)]
class TelefonoTest extends TestCase {

    public function test_debe_crear_telefono_valido(): void {
        $telefono = new Telefono("987654321");
        $this->assertEquals("987654321", $telefono->valor());
    }

    public function test_debe_fallar_si_tiene_letras(): void {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("El teléfono debe ser un número celular local válido (9 dígitos y empezar con 9).");
        
        new Telefono("98765432a");
    }

    public function test_debe_fallar_si_no_empieza_con_9(): void {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("El teléfono debe ser un número celular local válido (9 dígitos y empezar con 9).");
        
        new Telefono("887654321");
    }

    public function test_debe_fallar_si_no_tiene_nueve_digitos(): void {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("El teléfono debe ser un número celular local válido (9 dígitos y empezar con 9).");
        
        new Telefono("9876543210");
    }
    public function test_debe_retornar_true_si_los_telefonos_tienen_el_mismo_numero(): void {
        $t1 = new Telefono("987654321");
        $t2 = new Telefono("987654321");

        $this->assertTrue($t1->esIgualA($t2));
    }

    public function test_debe_retornar_false_si_los_telefonos_son_distintos(): void {
        $t1 = new Telefono("987654321");
        $t2 = new Telefono("911222333");

        $this->assertFalse($t1->esIgualA($t2));
    }
    public function testDeberiaLimpiarEspaciosEnBlancoAccidentales(): void
    {
        // Mandamos el teléfono con espacios al inicio y al final
        $telefonoConEspacios = "  912345678  ";
        
        $objetoTelefono = new Telefono($telefonoConEspacios);

        // Si tu código tiene un método getter o si evalúas la igualdad, 
        // asegúrate de que el valor final NO tenga los espacios.
        $this->assertSame("912345678", $objetoTelefono->valor());
    }
}
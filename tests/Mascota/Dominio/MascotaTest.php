<?php

declare(strict_types=1);

namespace Tests\Mascota\Dominio;

use App\Mascota\Dominio\Mascota;
use App\Mascota\Dominio\ObjetoValor\MascotaId;
use App\Cliente\Dominio\ObjetoValor\ClienteId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Mascota::class)]
final class MascotaTest extends TestCase
{
    public function test_debe_registrar_una_mascota_valida(): void
    {
        // 1. Arrange & Act (Preparar y Actuar)
        $mascota = Mascota::registrar(
            new MascotaId(1),
            new ClienteId(99), // Simulamos que el dueño es el cliente #99
            'Firulais',
            'Perro',
            15.5
        );

        // 2. Assert (Afirmar)
        $this->assertSame(1, $mascota->obtenerId()->valor());
        $this->assertSame('Firulais', $mascota->obtenerNombre());
        $this->assertSame('Perro', $mascota->obtenerEspecie());
        $this->assertSame(15.5, $mascota->obtenerPeso());
    }

    public function test_no_debe_permitir_un_nombre_vacio(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El nombre de la mascota no puede estar vacío.');

        // Intentamos registrar una mascota sin nombre
        Mascota::registrar(new MascotaId(2), new ClienteId(99), '   ', 'Gato', 4.0);
    }

    public function test_no_debe_permitir_un_peso_negativo_o_cero(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('El peso de la mascota debe ser mayor a 0.');

        // Intentamos registrar un loro que pesa 0 kilos
        Mascota::registrar(new MascotaId(3), new ClienteId(99), 'Paco', 'Loro', 0.0);
    }
}
<?php

declare(strict_types=1);

namespace Tests\Mascota\Aplicacion;

use App\Mascota\Aplicacion\RegistrarMascota;
use App\Mascota\Aplicacion\RegistrarMascotaPeticion;
use App\Mascota\Dominio\RepositorioMascota;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RegistrarMascota::class)]
final class RegistrarMascotaTest extends TestCase
{
    public function test_debe_registrar_una_mascota_correctamente(): void
    {
        // 1. Arrange (Preparar)
        // Creamos un simulador del repositorio
        $repositorioMock = $this->createMock(RepositorioMascota::class);
        
        // Le decimos que ESPERAMOS que el método 'guardar' sea llamado exactamente una vez
        $repositorioMock->expects($this->once())->method('guardar');

        $casoDeUso = new RegistrarMascota($repositorioMock);
        $peticion = new RegistrarMascotaPeticion(1, 99, 'Firulais', 'Perro', 15.5);

        // 2. Act (Actuar)
        $casoDeUso->ejecutar($peticion);

        // 3. Assert (Afirmar - Implícito en la expectativa del Mock)
    }
}
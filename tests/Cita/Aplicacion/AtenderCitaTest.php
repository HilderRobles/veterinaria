<?php
declare(strict_types=1);
namespace Tests\Cita\Aplicacion;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Cita\Aplicacion\AtenderCita;
use App\Cita\Dominio\RepositorioCita;
use App\Cita\Dominio\Cita;

#[CoversClass(AtenderCita::class)]
final class AtenderCitaTest extends TestCase
{
    public function test_ejecuta_atender_cita_y_actualiza(): void
    {
        $repositorioMock = $this->createMock(RepositorioCita::class);
        $cita = Cita::reconstituir(1, 1, 1, 1, '2028-10-10', '10:00:00', 'Motivo', 'confirmada');
        
        $repositorioMock->method('buscarPorId')->willReturn($cita);
        $repositorioMock->expects($this->once())->method('actualizar');

        $casoDeUso = new AtenderCita($repositorioMock);
        $casoDeUso->ejecutar(1);

        $this->assertSame('atendida', $cita->getEstado());
    }
}
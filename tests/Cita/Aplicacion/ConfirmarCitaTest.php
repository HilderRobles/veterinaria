<?php
declare(strict_types=1);
namespace Tests\Cita\Aplicacion;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Cita\Aplicacion\ConfirmarCita;
use App\Cita\Aplicacion\NotificadorCita;
use App\Cita\Dominio\RepositorioCita;
use App\Cita\Dominio\Cita;

#[CoversClass(ConfirmarCita::class)]
final class ConfirmarCitaTest extends TestCase
{
    public function test_ejecuta_confirmar_cita_y_actualiza(): void
    {
        $repositorioMock = $this->createMock(RepositorioCita::class);
        $notificadorMock = $this->createMock(NotificadorCita::class); // <-- Mock

        $cita = Cita::reconstituir(1, 1, 1, 1, '2028-10-10', '10:00:00', 'Motivo', 'pendiente');
        
        $repositorioMock->method('buscarPorId')->willReturn($cita);
        $repositorioMock->expects($this->once())->method('actualizar');
        // Verificamos que llame a notificar
        $notificadorMock->expects($this->once())->method('notificarConfirmacion');

        $casoDeUso = new ConfirmarCita($repositorioMock, $notificadorMock);
        $casoDeUso->ejecutar(1);

        $this->assertSame('confirmada', $cita->getEstado());
    }
}
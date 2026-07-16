<?php
declare(strict_types=1);
namespace Tests\Cita\Aplicacion;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Cita\Aplicacion\AgendarCita;
use App\Cita\Aplicacion\AgendarCitaPeticion;
use App\Cita\Aplicacion\NotificadorCita;
use App\Cita\Dominio\RepositorioCita;
use App\Cita\Dominio\Cita;

#[CoversClass(AgendarCita::class)]
final class AgendarCitaTest extends TestCase
{
    public function test_ejecuta_agendar_cita_y_guarda_en_repositorio(): void
    {
        $repositorioMock = $this->createMock(RepositorioCita::class);
        $notificadorMock = $this->createMock(NotificadorCita::class); // <-- Mock

        $repositorioMock->expects($this->once())->method('guardar');
        // Verificamos que sí llame a notificar
        $notificadorMock->expects($this->once())->method('notificarCreacion'); 

        $casoDeUso = new AgendarCita($repositorioMock, $notificadorMock);
        $peticion = new AgendarCitaPeticion(1, 1, 1, '2028-10-10', '14:00:00', 'Test');
        
        $casoDeUso->ejecutar($peticion);
    }
}
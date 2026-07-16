<?php
declare(strict_types=1);
namespace Tests\Cita\Dominio;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Cita\Dominio\Cita;
use Exception;

#[CoversClass(Cita::class)]
final class CitaTest extends TestCase
{
    public function test_debe_crear_cita_nueva_en_estado_pendiente(): void
    {
        $cita = Cita::crearNueva(1, 2, 3, '2028-10-10', '10:00:00', 'Vacuna');

        $this->assertNull($cita->getId());
        $this->assertSame(1, $cita->getIdCliente());
        $this->assertSame('pendiente', $cita->getEstado());
    }

    public function test_inv01_impide_confirmar_cita_cancelada(): void
    {
        $cita = Cita::reconstituir(1, 1, 1, 1, '2028-10-10', '10:00', 'Motivo', 'cancelada');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invariante Violada [INV-01]');
        $cita->confirmar();
    }

    public function test_permite_confirmar_y_cancelar_citas_pendientes(): void
    {
        $cita = Cita::reconstituir(1, 1, 1, 1, '2028-10-10', '10:00', 'Motivo', 'pendiente');
        
        $cita->confirmar();
        $this->assertSame('confirmada', $cita->getEstado());

        $cita->cancelar();
        $this->assertSame('cancelada', $cita->getEstado());
    }
    public function test_permite_atender_cita_no_cancelada(): void
    {
        // Simulamos una cita que ya fue confirmada
        $cita = Cita::reconstituir(1, 1, 1, 1, '2028-10-10', '10:00:00', 'Motivo', 'confirmada');
        
        $cita->atender();
        $this->assertSame('atendida', $cita->getEstado());
    }

    public function test_impide_atender_cita_cancelada(): void
    {
        // Simulamos una cita cancelada
        $cita = Cita::reconstituir(1, 1, 1, 1, '2028-10-10', '10:00:00', 'Motivo', 'cancelada');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Error de Dominio: Una cita cancelada no puede ser atendida.');
        
        $cita->atender();
    }

    public function test_impide_cancelar_una_cita_ya_atendida(): void
    {
        // Simulamos una cita que ya fue atendida por el doctor
        $cita = Cita::reconstituir(1, 1, 1, 1, '2028-10-10', '10:00:00', 'Motivo', 'atendida');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Error de Dominio: Una cita que ya fue atendida no puede ser cancelada.');
        
        $cita->cancelar();
    }

}
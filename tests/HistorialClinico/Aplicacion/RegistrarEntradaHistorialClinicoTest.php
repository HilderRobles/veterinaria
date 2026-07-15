<?php

namespace Tests\HistorialClinico\Aplicacion;

use App\HistorialClinico\Aplicacion\RegistrarEntradaHistorialClinico;
use App\HistorialClinico\Aplicacion\RegistrarEntradaHistorialClinicoPeticion;
use App\HistorialClinico\Dominio\EntradaHistorialClinico;
use App\HistorialClinico\Dominio\HistorialClinico;
use App\HistorialClinico\Dominio\RepositorioHistorialClinico;
use App\HistorialClinico\Dominio\ObjetoValor\Diagnostico;
use App\HistorialClinico\Dominio\ObjetoValor\HistorialClinicoId;
use App\HistorialClinico\Dominio\ObjetoValor\MascotaId;
use App\HistorialClinico\Dominio\ObjetoValor\Tratamiento;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RegistrarEntradaHistorialClinico::class)]
#[CoversClass(RegistrarEntradaHistorialClinicoPeticion::class)]
#[UsesClass(HistorialClinico::class)]
#[UsesClass(EntradaHistorialClinico::class)]
#[UsesClass(MascotaId::class)]
#[UsesClass(HistorialClinicoId::class)]
#[UsesClass(Diagnostico::class)]
#[UsesClass(Tratamiento::class)]
class RegistrarEntradaHistorialClinicoTest extends TestCase
{
    private $repositorioMock;
    private RegistrarEntradaHistorialClinico $casoUso;

    protected function setUp(): void
    {
        $this->repositorioMock = $this->createMock(RepositorioHistorialClinico::class);
        $this->casoUso = new RegistrarEntradaHistorialClinico($this->repositorioMock);
    }

    public function test_bdd_dado_una_mascota_sin_historial_cuando_se_registra_una_entrada_entonces_se_crea_un_historial_clinico(): void
    {
        // Given: una mascota no tiene historial clínico registrado.
        $peticion = new RegistrarEntradaHistorialClinicoPeticion(
            8,
            'Consulta por fiebre',
            'Infección respiratoria leve',
            'Reposo y medicación por 5 días',
            'Dr. Luis Ramos',
            new DateTimeImmutable('2026-06-11 10:00:00')
        );

        $this->repositorioMock
            ->method('buscarPorMascotaId')
            ->willReturn(null);

        $historialGuardado = null;

        $this->repositorioMock
            ->expects($this->once())
            ->method('guardar')
            ->willReturnCallback(function (HistorialClinico $historial) use (&$historialGuardado): void {
                $historialGuardado = $historial;
            });

        // When: se ejecuta el caso de uso para registrar una entrada clínica.
        $this->casoUso->ejecutar($peticion);

        // Then: el sistema crea un historial clínico nuevo con una entrada registrada.
        $this->assertInstanceOf(HistorialClinico::class, $historialGuardado);
        $this->assertSame(8, $historialGuardado->obtenerMascotaId()->valor());
        $this->assertSame(1, $historialGuardado->cantidadEntradas());
        $this->assertSame('Consulta por fiebre', $historialGuardado->obtenerUltimaEntrada()->obtenerMotivo());
        $this->assertSame('Dr. Luis Ramos', $historialGuardado->obtenerUltimaEntrada()->obtenerVeterinario());
    }

    public function test_bdd_dado_una_mascota_con_historial_cuando_se_registra_otra_entrada_entonces_se_agrega_al_historial_existente(): void
    {
        // Given: una mascota ya cuenta con un historial clínico existente.
        $historialExistente = new HistorialClinico(new MascotaId(8), new HistorialClinicoId(3));

        $historialExistente->agregarEntrada(new EntradaHistorialClinico(
            'Control previo',
            new Diagnostico('Paciente estable'),
            new Tratamiento('Control preventivo anual'),
            'Dra. Ana Torres'
        ));

        $peticion = new RegistrarEntradaHistorialClinicoPeticion(
            8,
            'Revisión de evolución',
            'Mejoría clínica general',
            'Continuar tratamiento indicado',
            'Dr. Luis Ramos'
        );

        $this->repositorioMock
            ->method('buscarPorMascotaId')
            ->willReturn($historialExistente);

        $historialGuardado = null;

        $this->repositorioMock
            ->expects($this->once())
            ->method('guardar')
            ->willReturnCallback(function (HistorialClinico $historial) use (&$historialGuardado): void {
                $historialGuardado = $historial;
            });

        // When: se registra una nueva entrada clínica para la misma mascota.
        $this->casoUso->ejecutar($peticion);

        // Then: el sistema conserva el historial existente y agrega la nueva entrada.
        $this->assertInstanceOf(HistorialClinico::class, $historialGuardado);
        $this->assertSame(3, $historialGuardado->obtenerId()?->valor());
        $this->assertSame(8, $historialGuardado->obtenerMascotaId()->valor());
        $this->assertSame(2, $historialGuardado->cantidadEntradas());
        $this->assertSame('Revisión de evolución', $historialGuardado->obtenerUltimaEntrada()->obtenerMotivo());
        $this->assertSame('Dr. Luis Ramos', $historialGuardado->obtenerUltimaEntrada()->obtenerVeterinario());
    }

    public function test_bdd_dado_un_id_de_mascota_invalido_cuando_se_intenta_registrar_una_entrada_entonces_se_rechaza_la_peticion(): void
    {
        // Given: una petición contiene un ID de mascota inválido.
        $peticion = new RegistrarEntradaHistorialClinicoPeticion(
            0,
            'Consulta general',
            'Paciente estable',
            'Control preventivo anual',
            'Dr. Luis Ramos'
        );

        // Then: el sistema debe rechazar la operación por regla de validación.
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('El ID de la mascota debe ser un número entero positivo.');

        // When: se intenta ejecutar el caso de uso con una mascota inválida.
        $this->casoUso->ejecutar($peticion);
    }
}
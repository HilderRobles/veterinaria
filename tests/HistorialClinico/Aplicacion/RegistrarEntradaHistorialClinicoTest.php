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
class RegistrarEntradaHistorialClinicoTest extends TestCase {
    private $repositorioMock;
    private RegistrarEntradaHistorialClinico $casoUso;

    protected function setUp(): void {
        $this->repositorioMock = $this->createMock(RepositorioHistorialClinico::class);
        $this->casoUso = new RegistrarEntradaHistorialClinico($this->repositorioMock);
    }

    public function test_debe_crear_historial_si_la_mascota_no_tiene_historial(): void {
        $peticion = new RegistrarEntradaHistorialClinicoPeticion(
            8,
            'Consulta por fiebre',
            'Infección respiratoria leve',
            'Reposo y medicación por 5 días',
            'Dr. Luis Ramos',
            new DateTimeImmutable('2026-06-11 10:00:00')
        );

        $this->repositorioMock->method('buscarPorMascotaId')->willReturn(null);

        $this->repositorioMock
            ->expects($this->once())
            ->method('guardar')
            ->with($this->callback(function (HistorialClinico $historial): bool {
                return $historial->obtenerMascotaId()->valor() === 8
                    && $historial->cantidadEntradas() === 1
                    && $historial->obtenerUltimaEntrada()->obtenerVeterinario() === 'Dr. Luis Ramos';
            }));

        $this->casoUso->ejecutar($peticion);
    }

    public function test_debe_agregar_entrada_a_historial_existente(): void {
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

        $this->repositorioMock->method('buscarPorMascotaId')->willReturn($historialExistente);

        $this->repositorioMock
            ->expects($this->once())
            ->method('guardar')
            ->with($this->callback(function (HistorialClinico $historial): bool {
                return $historial->cantidadEntradas() === 2
                    && $historial->obtenerUltimaEntrada()->obtenerMotivo() === 'Revisión de evolución';
            }));

        $this->casoUso->ejecutar($peticion);
    }

    public function test_debe_fallar_si_el_id_de_mascota_no_es_valido(): void {
        $peticion = new RegistrarEntradaHistorialClinicoPeticion(
            0,
            'Consulta general',
            'Paciente estable',
            'Control preventivo anual',
            'Dr. Luis Ramos'
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("El ID de la mascota debe ser un número entero positivo.");

        $this->casoUso->ejecutar($peticion);
    }
}

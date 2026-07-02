<?php

namespace Tests\HistorialClinico\Dominio;

use App\HistorialClinico\Dominio\EntradaHistorialClinico;
use App\HistorialClinico\Dominio\HistorialClinico;
use App\HistorialClinico\Dominio\ObjetoValor\Diagnostico;
use App\HistorialClinico\Dominio\ObjetoValor\HistorialClinicoId;
use App\HistorialClinico\Dominio\ObjetoValor\MascotaId;
use App\HistorialClinico\Dominio\ObjetoValor\Tratamiento;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(HistorialClinico::class)]
#[CoversClass(EntradaHistorialClinico::class)]
#[UsesClass(HistorialClinicoId::class)]
#[UsesClass(MascotaId::class)]
#[UsesClass(Diagnostico::class)]
#[UsesClass(Tratamiento::class)]
class HistorialClinicoTest extends TestCase {
    private function crearEntradaValida(): EntradaHistorialClinico {
        return new EntradaHistorialClinico(
            'Consulta por dolor de oído',
            new Diagnostico('Otitis externa leve'),
            new Tratamiento('Aplicar gotas cada 12 horas'),
            'Dra. Ana Torres',
            new DateTimeImmutable('2026-06-11 09:30:00')
        );
    }

    public function test_debe_crear_historial_clinico_sin_entradas(): void {
        $mascotaId = new MascotaId(5);
        $historial = new HistorialClinico($mascotaId);

        $this->assertNull($historial->obtenerId());
        $this->assertSame($mascotaId, $historial->obtenerMascotaId());
        $this->assertCount(0, $historial->obtenerEntradas());
        $this->assertEquals(0, $historial->cantidadEntradas());
        $this->assertNull($historial->obtenerUltimaEntrada());
    }

    public function test_debe_asignar_id_solo_una_vez(): void {
        $historial = new HistorialClinico(new MascotaId(5));
        $historial->asignarId(new HistorialClinicoId(1));

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("El historial clínico ya tiene un ID asignado.");

        $historial->asignarId(new HistorialClinicoId(2));
    }

    public function test_debe_agregar_entrada_clinica_correctamente(): void {
        $historial = new HistorialClinico(new MascotaId(5), new HistorialClinicoId(1));
        $entrada = $this->crearEntradaValida();

        $historial->agregarEntrada($entrada);

        $this->assertEquals(1, $historial->cantidadEntradas());
        $this->assertSame($entrada, $historial->obtenerUltimaEntrada());
    }

    public function test_debe_mapear_historial_a_arreglo(): void {
        $historial = new HistorialClinico(new MascotaId(5), new HistorialClinicoId(1));
        $historial->agregarEntrada($this->crearEntradaValida());

        $arreglo = $historial->mapearAArreglo();

        $this->assertEquals(1, $arreglo['id']);
        $this->assertEquals(5, $arreglo['mascota_id']);
        $this->assertCount(1, $arreglo['entradas']);
        $this->assertEquals('Consulta por dolor de oído', $arreglo['entradas'][0]['motivo']);
        $this->assertEquals('Otitis externa leve', $arreglo['entradas'][0]['diagnostico']);
    }

    public function test_no_debe_permitir_motivo_vacio(): void {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("El motivo de atención no puede estar vacío.");

        new EntradaHistorialClinico(
            '   ',
            new Diagnostico('Otitis externa leve'),
            new Tratamiento('Aplicar gotas cada 12 horas'),
            'Dra. Ana Torres'
        );
    }

    public function test_no_debe_permitir_veterinario_vacio(): void {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("El veterinario responsable no puede estar vacío.");

        new EntradaHistorialClinico(
            'Consulta general',
            new Diagnostico('Paciente estable'),
            new Tratamiento('Control preventivo anual'),
            '   '
        );
    }
}

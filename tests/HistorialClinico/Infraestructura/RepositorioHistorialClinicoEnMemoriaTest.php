<?php

namespace Tests\HistorialClinico\Infraestructura;

use App\HistorialClinico\Dominio\EntradaHistorialClinico;
use App\HistorialClinico\Dominio\HistorialClinico;
use App\HistorialClinico\Dominio\ObjetoValor\Diagnostico;
use App\HistorialClinico\Dominio\ObjetoValor\HistorialClinicoId;
use App\HistorialClinico\Dominio\ObjetoValor\MascotaId;
use App\HistorialClinico\Dominio\ObjetoValor\Tratamiento;
use App\HistorialClinico\Infraestructura\RepositorioHistorialClinicoEnMemoria;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RepositorioHistorialClinicoEnMemoria::class)]
#[UsesClass(HistorialClinico::class)]
#[UsesClass(EntradaHistorialClinico::class)]
#[UsesClass(HistorialClinicoId::class)]
#[UsesClass(MascotaId::class)]
#[UsesClass(Diagnostico::class)]
#[UsesClass(Tratamiento::class)]
class RepositorioHistorialClinicoEnMemoriaTest extends TestCase {
    public function test_debe_guardar_y_buscar_historial_por_id_y_mascota(): void {
        $repositorio = new RepositorioHistorialClinicoEnMemoria();
        $historial = new HistorialClinico(new MascotaId(15));
        $historial->agregarEntrada(new EntradaHistorialClinico(
            'Consulta por alergia',
            new Diagnostico('Dermatitis leve'),
            new Tratamiento('Baño medicado semanal'),
            'Dra. Ana Torres'
        ));

        $repositorio->guardar($historial);

        $this->assertNotNull($historial->obtenerId());
        $this->assertSame($historial, $repositorio->buscarPorId($historial->obtenerId()));
        $this->assertSame($historial, $repositorio->buscarPorMascotaId(new MascotaId(15)));
    }

    public function test_no_debe_actualizar_historial_sin_id(): void {
        $repositorio = new RepositorioHistorialClinicoEnMemoria();
        $historial = new HistorialClinico(new MascotaId(15));

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage("No se puede actualizar un historial clínico sin ID.");

        $repositorio->actualizar($historial);
    }
}

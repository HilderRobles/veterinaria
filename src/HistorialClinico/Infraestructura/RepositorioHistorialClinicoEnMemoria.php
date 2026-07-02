<?php

namespace App\HistorialClinico\Infraestructura;

use App\HistorialClinico\Dominio\HistorialClinico;
use App\HistorialClinico\Dominio\RepositorioHistorialClinico;
use App\HistorialClinico\Dominio\ObjetoValor\HistorialClinicoId;
use App\HistorialClinico\Dominio\ObjetoValor\MascotaId;

class RepositorioHistorialClinicoEnMemoria implements RepositorioHistorialClinico {
    /** @var array<int, HistorialClinico> */
    private array $historialesPorId = [];

    /** @var array<int, HistorialClinico> */
    private array $historialesPorMascota = [];

    private int $secuencia = 1;

    public function guardar(HistorialClinico $historial): void {
        if ($historial->obtenerId() === null) {
            $historial->asignarId(new HistorialClinicoId($this->secuencia++));
        }

        $this->actualizar($historial);
    }

    public function buscarPorId(HistorialClinicoId $id): ?HistorialClinico {
        return $this->historialesPorId[$id->valor()] ?? null;
    }

    public function buscarPorMascotaId(MascotaId $mascotaId): ?HistorialClinico {
        return $this->historialesPorMascota[$mascotaId->valor()] ?? null;
    }

    public function actualizar(HistorialClinico $historial): void {
        if ($historial->obtenerId() === null) {
            throw new \DomainException("No se puede actualizar un historial clínico sin ID.");
        }

        $id = $historial->obtenerId()->valor();
        $mascotaId = $historial->obtenerMascotaId()->valor();

        $this->historialesPorId[$id] = $historial;
        $this->historialesPorMascota[$mascotaId] = $historial;
    }
}

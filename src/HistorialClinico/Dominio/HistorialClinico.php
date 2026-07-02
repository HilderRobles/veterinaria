<?php

namespace App\HistorialClinico\Dominio;

use App\HistorialClinico\Dominio\ObjetoValor\HistorialClinicoId;
use App\HistorialClinico\Dominio\ObjetoValor\MascotaId;

class HistorialClinico {
    private ?HistorialClinicoId $id;
    private MascotaId $mascotaId;

    /** @var EntradaHistorialClinico[] */
    private array $entradas;

    /**
     * @param EntradaHistorialClinico[] $entradas
     */
    public function __construct(MascotaId $mascotaId, ?HistorialClinicoId $id = null, array $entradas = []) {
        $this->mascotaId = $mascotaId;
        $this->id = $id;
        $this->entradas = [];

        foreach ($entradas as $entrada) {
            $this->agregarEntrada($entrada);
        }
    }

    public function asignarId(HistorialClinicoId $id): void {
        if ($this->id !== null) {
            throw new \DomainException("El historial clínico ya tiene un ID asignado.");
        }

        $this->id = $id;
    }

    public function agregarEntrada(EntradaHistorialClinico $entrada): void {
        $this->entradas[] = $entrada;
    }

    public function obtenerId(): ?HistorialClinicoId { return $this->id; }
    public function obtenerMascotaId(): MascotaId { return $this->mascotaId; }

    /** @return EntradaHistorialClinico[] */
    public function obtenerEntradas(): array { return $this->entradas; }

    public function cantidadEntradas(): int { return count($this->entradas); }

    public function obtenerUltimaEntrada(): ?EntradaHistorialClinico {
        if ($this->entradas === []) {
            return null;
        }

        return $this->entradas[array_key_last($this->entradas)];
    }

    public function mapearAArreglo(): array {
        return [
            'id' => $this->id ? $this->id->valor() : null,
            'mascota_id' => $this->mascotaId->valor(),
            'entradas' => array_map(
                fn (EntradaHistorialClinico $entrada) => $entrada->mapearAArreglo(),
                $this->entradas
            )
        ];
    }
}

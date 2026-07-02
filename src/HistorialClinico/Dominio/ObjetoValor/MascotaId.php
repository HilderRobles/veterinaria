<?php

namespace App\HistorialClinico\Dominio\ObjetoValor;

final class MascotaId {
    public function __construct(private int $valor) {
        if ($valor <= 0) {
            throw new \InvalidArgumentException("El ID de la mascota debe ser un número entero positivo.");
        }
    }

    public function valor(): int {
        return $this->valor;
    }
}

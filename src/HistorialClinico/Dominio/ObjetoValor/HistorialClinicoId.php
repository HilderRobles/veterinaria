<?php

namespace App\HistorialClinico\Dominio\ObjetoValor;

final class HistorialClinicoId {
    public function __construct(private int $valor) {
        if ($valor <= 0) {
            throw new \InvalidArgumentException("El ID del historial clínico debe ser un número entero positivo.");
        }
    }

    public function valor(): int {
        return $this->valor;
    }
}

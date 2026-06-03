<?php

namespace App\Cliente\Dominio\ObjetoValor;

class ClienteId {
    public function __construct(private int $valor) {
        if ($valor <= 0) {
            throw new \InvalidArgumentException("El ID del cliente debe ser un número entero positivo.");
        }
    }

    public function valor(): int { 
        return $this->valor; 
    }
}
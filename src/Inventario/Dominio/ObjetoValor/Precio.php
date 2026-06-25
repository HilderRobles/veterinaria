<?php

namespace App\Inventario\Dominio\ObjetoValor;

class Precio {
    public function __construct(private float $valor) {
        if ($valor <= 0) {
            throw new \DomainException("El precio del producto debe ser mayor a cero.");
        }
    }

    public function valor(): float {
        return $this->valor;
    }
}
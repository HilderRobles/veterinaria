<?php

namespace App\HistorialClinico\Dominio\ObjetoValor;

final class Tratamiento {
    private string $valor;

    public function __construct(string $valor) {
        $valorLimpio = trim($valor);

        if ($valorLimpio === '') {
            throw new \DomainException("El tratamiento no puede estar vacío.");
        }

        if (mb_strlen($valorLimpio) < 5) {
            throw new \DomainException("El tratamiento debe tener al menos 5 caracteres.");
        }

        $this->valor = $valorLimpio;
    }

    public function valor(): string {
        return $this->valor;
    }
}

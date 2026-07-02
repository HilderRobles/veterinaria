<?php

namespace App\HistorialClinico\Dominio\ObjetoValor;

final class Diagnostico {
    private string $valor;

    public function __construct(string $valor) {
        $valorLimpio = trim($valor);

        if ($valorLimpio === '') {
            throw new \DomainException("El diagnóstico no puede estar vacío.");
        }

        if (mb_strlen($valorLimpio) < 5) {
            throw new \DomainException("El diagnóstico debe tener al menos 5 caracteres.");
        }

        $this->valor = $valorLimpio;
    }

    public function valor(): string {
        return $this->valor;
    }
}

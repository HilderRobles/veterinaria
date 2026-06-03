<?php

namespace App\Cliente\Dominio\ObjetoValor;

class Telefono {
    public function __construct(private string $valor) {
        if (!preg_match('/^[0-9]{10}$/', $valor)) {
            throw new \DomainException("El teléfono debe contener exactamente 10 dígitos numéricos.");
        }
    }

    public function valor(): string { 
        return $this->valor; 
    }
}
<?php

namespace App\Cliente\Dominio\ObjetoValor;

final class Contrasena {
    public function __construct(private string $valor) {
        if (empty(trim($valor))) {
            throw new \DomainException("La contraseña no puede estar vacía.");
        }
    }

    public function valor(): string { 
        return $this->valor; 
    }
    
    public function verificar(string $contrasenaPlana): bool {
        return password_verify($contrasenaPlana, $this->valor);
    }
}
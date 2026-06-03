<?php

namespace App\Cliente\Dominio\ObjetoValor;

final class CorreoElectronico {
    private string $valor;

    public function __construct(string $valor) {
        // 💡 Primero limpiamos los espacios y normalizamos a minúsculas
        $valorLimpio = strtolower(trim($valor));

        // 💡 Ahora validamos el string ya procesado
        if (!filter_var($valorLimpio, FILTER_VALIDATE_EMAIL)) {
            throw new \DomainException("El formato del correo electrónico no es válido.");
        }
        
        $this->valor = $valorLimpio;
    }

    public function valor(): string { 
        return $this->valor; 
    }
}
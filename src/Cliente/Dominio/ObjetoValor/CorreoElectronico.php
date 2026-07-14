<?php

namespace App\Cliente\Dominio\ObjetoValor;

final class CorreoElectronico 
{
    private string $valor;

    public function __construct(string $valor) 
    {
        // 💡 Todo a minúsculas: Gon y gon terminan siendo lo mismo en la base de datos
        $valorLimpio = strtolower(trim($valor));

        if (!filter_var($valorLimpio, FILTER_VALIDATE_EMAIL)) {
            throw new \DomainException("El formato del correo electrónico no es válido.");
        }
        
        $this->valor = $valorLimpio;
    }

    public function valor(): string 
    { 
        return $this->valor; 
    }

    public function esIgualA(CorreoElectronico $otroCorreo): bool 
    {
        return $this->valor === $otroCorreo->valor();
    }
}
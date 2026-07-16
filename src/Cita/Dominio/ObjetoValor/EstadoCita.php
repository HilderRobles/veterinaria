<?php

declare(strict_types=1);

namespace App\Cita\Dominio\ObjetoValor;

use Exception;

final class EstadoCita
{
    private string $valor;

    public function __construct(string $valor)
    {
        $valoresValidos = ['pendiente', 'confirmada', 'atendida', 'cancelada'];
        $valor = strtolower($valor);

        if (!in_array($valor, $valoresValidos, true)) {
            throw new Exception("Error de Dominio: El estado '{$valor}' no es válido.");
        }

        $this->valor = $valor;
    }

    public function getValor(): string
    {
        return $this->valor;
    }

    public function equals(EstadoCita $otro): bool
    {
        return $this->valor === $otro->getValor();
    }
}
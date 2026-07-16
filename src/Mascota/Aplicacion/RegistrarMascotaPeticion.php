<?php

declare(strict_types=1);

namespace App\Mascota\Aplicacion;

final class RegistrarMascotaPeticion
{
    public function __construct(
        public readonly int $id,
        public readonly int $clienteId,
        public readonly string $nombre,
        public readonly string $especie,
        public readonly float $peso
    ) {
    }
    
}
<?php

declare(strict_types=1);

namespace App\Mascota\Dominio\Excepciones;

use App\Mascota\Dominio\ObjetoValor\MascotaId;
use RuntimeException;

final class MascotaNoEncontrada extends RuntimeException
{
    public function __construct(MascotaId $id)
    {
        parent::__construct(sprintf('No se encontró ninguna mascota con el ID "%d".', $id->valor()));
    }
}
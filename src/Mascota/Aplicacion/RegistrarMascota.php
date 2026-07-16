<?php

declare(strict_types=1);

namespace App\Mascota\Aplicacion;

use App\Mascota\Dominio\Mascota;
use App\Mascota\Dominio\ObjetoValor\MascotaId;
use App\Cliente\Dominio\ObjetoValor\ClienteId;
use App\Mascota\Dominio\RepositorioMascota;

final class RegistrarMascota
{
    public function __construct(private RepositorioMascota $repositorio)
    {
    }

    public function ejecutar(RegistrarMascotaPeticion $peticion): void
    {
        $mascota = Mascota::registrar(
            new MascotaId($peticion->id),
            new ClienteId($peticion->clienteId),
            $peticion->nombre,
            $peticion->especie,
            $peticion->peso
        );

        $this->repositorio->guardar($mascota);
    }
}
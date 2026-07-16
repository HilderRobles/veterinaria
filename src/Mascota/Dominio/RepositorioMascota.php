<?php

declare(strict_types=1);

namespace App\Mascota\Dominio;

use App\Cliente\Dominio\ObjetoValor\ClienteId;
use App\Mascota\Dominio\ObjetoValor\MascotaId;

interface RepositorioMascota
{
    public function guardar(Mascota $mascota): void;

    public function buscarPorId(MascotaId $id): ?Mascota;

    /**
     * @return Mascota[]
     */
    public function buscarPorCliente(ClienteId $clienteId): array;

    public function eliminar(MascotaId $id): void;
}
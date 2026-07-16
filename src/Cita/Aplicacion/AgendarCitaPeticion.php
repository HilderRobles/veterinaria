<?php
declare(strict_types=1);
namespace App\Cita\Aplicacion;

final class AgendarCitaPeticion
{
    public function __construct(
        public readonly int $idCliente,
        public readonly int $idMascota,
        public readonly int $idServicio,
        public readonly string $fecha,
        public readonly string $hora,
        public readonly string $motivo
    ) {}
}
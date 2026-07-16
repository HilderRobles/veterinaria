<?php
declare(strict_types=1);
namespace App\Cita\Aplicacion;

use App\Cita\Dominio\Cita;
use App\Cita\Dominio\RepositorioCita;

final class AgendarCita
{
    public function __construct(
        private RepositorioCita $repositorio,
        private NotificadorCita $notificador
    ) {}

    public function ejecutar(AgendarCitaPeticion $peticion): void
    {
        $cita = Cita::crearNueva(
            $peticion->idCliente,
            $peticion->idMascota,
            $peticion->idServicio,
            $peticion->fecha,
            $peticion->hora,
            $peticion->motivo
        );
        $this->repositorio->guardar($cita);
        $this->notificador->notificarCreacion($cita);
    }
}
<?php
declare(strict_types=1);
namespace App\Cita\Aplicacion;

use App\Cita\Dominio\Cita;

interface NotificadorCita
{
    public function notificarCreacion(Cita $cita): void;
    public function notificarConfirmacion(Cita $cita): void;
}
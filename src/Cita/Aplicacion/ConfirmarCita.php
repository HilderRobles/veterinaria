<?php
declare(strict_types=1);
namespace App\Cita\Aplicacion;

use App\Cita\Dominio\RepositorioCita;
use Exception;

final class ConfirmarCita
{
    public function __construct(
        private RepositorioCita $repositorio,
        private NotificadorCita $notificador
    ) {}

    public function ejecutar(int $idCita): void
    {
        $cita = $this->repositorio->buscarPorId($idCita);
        if (!$cita) throw new Exception("La cita con ID {$idCita} no existe.");
        
        $cita->confirmar();
        $this->repositorio->actualizar($cita);
        $this->notificador->notificarConfirmacion($cita);
    }
}
<?php
declare(strict_types=1);
namespace App\Cita\Aplicacion;

use App\Cita\Dominio\RepositorioCita;
use Exception;

final class CancelarCita
{
    public function __construct(private RepositorioCita $repositorio) {}

    public function ejecutar(int $idCita): void
    {
        $cita = $this->repositorio->buscarPorId($idCita);
        if (!$cita) throw new Exception("La cita con ID {$idCita} no existe.");
        
        $cita->cancelar(); // Cambia el estado a 'cancelada'
        $this->repositorio->actualizar($cita);
    }
}
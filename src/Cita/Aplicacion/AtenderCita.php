<?php
declare(strict_types=1);
namespace App\Cita\Aplicacion;

use App\Cita\Dominio\RepositorioCita;
use Exception;

final class AtenderCita
{
    public function __construct(private RepositorioCita $repositorio) {}

    public function ejecutar(int $idCita): void
    {
        $cita = $this->repositorio->buscarPorId($idCita);
        if (!$cita) throw new Exception("La cita con ID {$idCita} no existe.");
        
        $cita->atender();
        $this->repositorio->actualizar($cita);
        
        // (Opcional a futuro): Aquí podríamos emitir un "Evento de Dominio" 
        // para que el módulo Historial Clínico se entere y cree una ficha vacía automáticamente.
    }
}
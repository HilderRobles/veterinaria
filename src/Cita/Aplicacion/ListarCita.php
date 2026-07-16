<?php
declare(strict_types=1);
namespace App\Cita\Aplicacion;

use App\Cita\Dominio\RepositorioCita;

final class ListarCita
{
    public function __construct(private RepositorioCita $repositorio) {}

    public function ejecutar(): array
    {
        $citas = $this->repositorio->listarTodas();
        
        // Convertimos los objetos a arrays para el JSON
        return array_map(function($cita) {
            return [
                'id_cita' => $cita->getId(),
                'id_cliente' => $cita->getIdCliente(),
                'id_mascota' => $cita->getIdMascota(),
                'fecha' => $cita->getFecha(),
                'hora' => $cita->getHora(),
                'estado' => $cita->getEstado()
            ];
        }, $citas);
    }
}
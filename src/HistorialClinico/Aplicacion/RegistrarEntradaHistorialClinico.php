<?php

namespace App\HistorialClinico\Aplicacion;

use App\HistorialClinico\Dominio\EntradaHistorialClinico;
use App\HistorialClinico\Dominio\HistorialClinico;
use App\HistorialClinico\Dominio\RepositorioHistorialClinico;
use App\HistorialClinico\Dominio\ObjetoValor\Diagnostico;
use App\HistorialClinico\Dominio\ObjetoValor\MascotaId;
use App\HistorialClinico\Dominio\ObjetoValor\Tratamiento;

class RegistrarEntradaHistorialClinico {
    public function __construct(private RepositorioHistorialClinico $repositorio) {}

    public function ejecutar(RegistrarEntradaHistorialClinicoPeticion $peticion): void {
        $mascotaId = new MascotaId($peticion->mascotaId);
        $historial = $this->repositorio->buscarPorMascotaId($mascotaId);

        if ($historial === null) {
            $historial = new HistorialClinico($mascotaId);
        }

        $entrada = new EntradaHistorialClinico(
            $peticion->motivo,
            new Diagnostico($peticion->diagnostico),
            new Tratamiento($peticion->tratamiento),
            $peticion->veterinario,
            $peticion->fechaAtencion
        );

        $historial->agregarEntrada($entrada);
        $this->repositorio->guardar($historial);
    }
}

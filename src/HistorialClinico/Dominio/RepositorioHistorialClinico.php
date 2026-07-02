<?php

namespace App\HistorialClinico\Dominio;

use App\HistorialClinico\Dominio\ObjetoValor\HistorialClinicoId;
use App\HistorialClinico\Dominio\ObjetoValor\MascotaId;

interface RepositorioHistorialClinico {
    public function guardar(HistorialClinico $historial): void;
    public function buscarPorId(HistorialClinicoId $id): ?HistorialClinico;
    public function buscarPorMascotaId(MascotaId $mascotaId): ?HistorialClinico;
    public function actualizar(HistorialClinico $historial): void;
}

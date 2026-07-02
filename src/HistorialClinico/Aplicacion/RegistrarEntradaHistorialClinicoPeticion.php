<?php

namespace App\HistorialClinico\Aplicacion;

use DateTimeImmutable;

class RegistrarEntradaHistorialClinicoPeticion {
    public function __construct(
        public int $mascotaId,
        public string $motivo,
        public string $diagnostico,
        public string $tratamiento,
        public string $veterinario,
        public ?DateTimeImmutable $fechaAtencion = null
    ) {}
}

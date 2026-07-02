<?php

namespace App\HistorialClinico\Dominio;

use App\HistorialClinico\Dominio\ObjetoValor\Diagnostico;
use App\HistorialClinico\Dominio\ObjetoValor\Tratamiento;
use DateTimeImmutable;

class EntradaHistorialClinico {
    private DateTimeImmutable $fechaAtencion;
    private string $motivo;
    private Diagnostico $diagnostico;
    private Tratamiento $tratamiento;
    private string $veterinario;

    public function __construct(
        string $motivo,
        Diagnostico $diagnostico,
        Tratamiento $tratamiento,
        string $veterinario,
        ?DateTimeImmutable $fechaAtencion = null
    ) {
        if (trim($motivo) === '') {
            throw new \DomainException("El motivo de atención no puede estar vacío.");
        }

        if (trim($veterinario) === '') {
            throw new \DomainException("El veterinario responsable no puede estar vacío.");
        }

        $this->motivo = trim($motivo);
        $this->diagnostico = $diagnostico;
        $this->tratamiento = $tratamiento;
        $this->veterinario = trim($veterinario);
        $this->fechaAtencion = $fechaAtencion ?? new DateTimeImmutable();
    }

    public function obtenerFechaAtencion(): DateTimeImmutable { return $this->fechaAtencion; }
    public function obtenerMotivo(): string { return $this->motivo; }
    public function obtenerDiagnostico(): Diagnostico { return $this->diagnostico; }
    public function obtenerTratamiento(): Tratamiento { return $this->tratamiento; }
    public function obtenerVeterinario(): string { return $this->veterinario; }

    public function mapearAArreglo(): array {
        return [
            'fecha_atencion' => $this->fechaAtencion->format('Y-m-d H:i:s'),
            'motivo' => $this->motivo,
            'diagnostico' => $this->diagnostico->valor(),
            'tratamiento' => $this->tratamiento->valor(),
            'veterinario' => $this->veterinario
        ];
    }
}

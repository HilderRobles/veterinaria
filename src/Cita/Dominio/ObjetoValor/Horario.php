<?php

declare(strict_types=1);

namespace App\Cita\Dominio\ObjetoValor;

use Exception;
use DateTimeImmutable;

final class Horario
{
    private string $fecha;
    private string $hora;

    public function __construct(string $fecha, string $hora)
    {
        // 1. Validamos que la hora esté en el bloque de atención de la clínica (ej. 8 AM a 6 PM)
        $horaInt = (int) substr($hora, 0, 2); // Extrae la hora, ej: "14" de "14:30:00"
        
        if ($horaInt < 8 || $horaInt >= 18) {
            throw new Exception("Error de Dominio: El horario debe estar dentro del bloque de atención habilitado (08:00 - 18:00).");
        }

        // 2. Regla INV-02: Consistencia Cronológica de Reserva Activa (No agendar en el pasado)
        $fechaHoraCita = new DateTimeImmutable("{$fecha} {$hora}");
        $ahora = new DateTimeImmutable();
        
        if ($fechaHoraCita < $ahora) {
            throw new Exception("Invariante Violada [INV-02]: No se puede agendar una cita en el pasado.");
        }

        $this->fecha = $fecha;
        $this->hora = $hora;
    }

    public function getFecha(): string
    {
        return $this->fecha;
    }

    public function getHora(): string
    {
        return $this->hora;
    }
}
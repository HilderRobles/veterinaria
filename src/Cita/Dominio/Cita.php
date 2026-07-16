<?php

declare(strict_types=1);

namespace App\Cita\Dominio;

use App\Cita\Dominio\ObjetoValor\EstadoCita;
use App\Cita\Dominio\ObjetoValor\Horario;
use Exception;

final class Cita
{
    private function __construct(
        private ?int $id,
        private int $idCliente,
        private int $idMascota,
        private int $idServicio,
        private string $fecha,
        private string $hora,
        private string $motivo,
        private EstadoCita $estado
    ) {}

    public static function crearNueva(
        int $idCliente,
        int $idMascota,
        int $idServicio,
        string $fecha,
        string $hora,
        string $motivo
    ): self {
        // 1. Delegamos TODA la validación de fecha y hora al Value Object Horario
        // Aquí adentro se valida que no sea en el pasado y que esté en horas de trabajo.
        $horario = new Horario($fecha, $hora);

        return new self(
            null, // El ID se asignará en la base de datos
            $idCliente,
            $idMascota,
            $idServicio,
            $horario->getFecha(),
            $horario->getHora(),
            $motivo,
            new EstadoCita('pendiente') // Por regla inicia pendiente
        );
    }

    public static function reconstituir(
        int $id, int $idCliente, int $idMascota, int $idServicio,
        string $fecha, string $hora, string $motivo, string $estado
    ): self {
        return new self($id, $idCliente, $idMascota, $idServicio, $fecha, $hora, $motivo, new EstadoCita($estado));
    }

    public function confirmar(): void
    {
        // [INV-01] Restricción de Flujo
        if ($this->estado->getValor() === 'cancelada') {
            throw new Exception("Invariante Violada [INV-01]: Una cita cancelada no puede ser confirmada.");
        }
        $this->estado = new EstadoCita('confirmada');
    }

    public function cancelar(): void
    {
        $estadoActual = $this->estado->getValor();

        // Regla 1: No puedes cancelar algo que ya terminó con éxito
        if ($estadoActual === 'atendida') {
            throw new Exception("Error de Dominio: Una cita que ya fue atendida no puede ser cancelada.");
        }

        // Regla 2: No tiene sentido cancelar algo que YA está cancelado
        if ($estadoActual === 'cancelada') {
            throw new Exception("Error de Dominio: La cita ya se encuentra cancelada.");
        }

        $this->estado = new EstadoCita('cancelada');
    }

    public function atender(): void
    {
        // Regla de Negocio: No puedes atender a un paciente si la cita fue cancelada
        if ($this->estado->getValor() === 'cancelada') {
            throw new Exception("Error de Dominio: Una cita cancelada no puede ser atendida.");
        }
        $this->estado = new EstadoCita('atendida');
    }


    // Getters de solo lectura
    public function getId(): ?int { return $this->id; }
    public function getIdCliente(): int { return $this->idCliente; }
    public function getIdMascota(): int { return $this->idMascota; }
    public function getIdServicio(): int { return $this->idServicio; }
    public function getFecha(): string { return $this->fecha; }
    public function getHora(): string { return $this->hora; }
    public function getMotivo(): string { return $this->motivo; }
    public function getEstado(): string { return $this->estado->getValor(); }
}
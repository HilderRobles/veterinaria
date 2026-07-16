<?php

declare(strict_types=1);

namespace App\Cita\Infraestructura;

use App\Cita\Dominio\Cita;
use App\Cita\Dominio\RepositorioCita;
use PDO;

final class PdoRepositorioCita implements RepositorioCita
{
    public function __construct(private PDO $pdo) {}

    public function guardar(Cita $cita): void
    {
        $sql = "INSERT INTO citas (id_cliente, id_mascota, id_servicio, fecha, hora, motivo, estado_cita) 
                VALUES (:cliente, :mascota, :servicio, :fecha, :hora, :motivo, :estado)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'cliente'  => $cita->getIdCliente(),
            'mascota'  => $cita->getIdMascota(),
            'servicio' => $cita->getIdServicio(),
            'fecha'    => $cita->getFecha(),
            'hora'     => $cita->getHora(),
            'motivo'   => $cita->getMotivo(),
            'estado'   => $cita->getEstado()
        ]);
    }

    public function actualizar(Cita $cita): void
    {
        $sql = "UPDATE citas SET estado_cita = :estado WHERE id_cita = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'estado' => $cita->getEstado(),
            'id'     => $cita->getId()
        ]);
    }

    public function buscarPorId(int $id): ?Cita
    {
        $sql = "SELECT * FROM citas WHERE id_cita = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return Cita::reconstituir(
            (int) $row['id_cita'],
            (int) $row['id_cliente'],
            (int) $row['id_mascota'],
            (int) $row['id_servicio'],
            $row['fecha'],
            $row['hora'],
            $row['motivo'] ?? '',
            $row['estado_cita']
        );
    }

    public function listarTodas(): array
    {
        $sql = "SELECT * FROM citas ORDER BY fecha DESC, hora DESC";
        $stmt = $this->pdo->query($sql);
        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $citas = [];
        foreach ($filas as $row) {
            $citas[] = Cita::reconstituir(
                (int) $row['id_cita'],
                (int) $row['id_cliente'],
                (int) $row['id_mascota'],
                (int) $row['id_servicio'],
                $row['fecha'],
                $row['hora'],
                $row['motivo'] ?? '',
                $row['estado_cita']
            );
        }
        return $citas;
    }
}
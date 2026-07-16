<?php

declare(strict_types=1);

namespace App\Mascota\Infraestructura;

use App\Mascota\Dominio\Mascota;
use App\Mascota\Dominio\ObjetoValor\MascotaId;
use App\Cliente\Dominio\ObjetoValor\ClienteId;
use App\Mascota\Dominio\RepositorioMascota;
use PDO;

final class RepositorioMascotaMySQL implements RepositorioMascota
{
    public function __construct(private PDO $conexion)
    {
    }

    public function guardar(Mascota $mascota): void
    {
        $stmt = $this->conexion->prepare(
            "INSERT INTO mascotas (id, cliente_id, nombre, especie, peso) VALUES (?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE cliente_id = ?, nombre = ?, especie = ?, peso = ?"
        );

        $stmt->execute([
            $mascota->obtenerId()->valor(),
            $mascota->obtenerClienteId()->valor(),
            $mascota->obtenerNombre(),
            $mascota->obtenerEspecie(),
            $mascota->obtenerPeso(),
            $mascota->obtenerClienteId()->valor(),
            $mascota->obtenerNombre(),
            $mascota->obtenerEspecie(),
            $mascota->obtenerPeso(),
        ]);
    }

    // --- MÉTODOS FALTANTES PARA CUMPLIR EL CONTRATO DE LA INTERFAZ ---

    public function buscarPorId(MascotaId $id): ?Mascota
    {
        // Por ahora lo dejamos retornando null o vacío para que pase la validación
        return null; 
    }

    public function buscarPorCliente(ClienteId $clienteId): array
    {
        return [];
    }

    public function eliminar(MascotaId $id): void
    {
        // Lógica futura de eliminación
    }
}
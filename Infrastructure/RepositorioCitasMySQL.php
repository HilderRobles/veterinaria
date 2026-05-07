<?php
namespace Infrastructure;

use Domain\RepositorioCitas;
use Domain\Cita;
use PDO;

class RepositorioCitasMySQL implements RepositorioCitas {
    private $conn;

    public function __construct(PDO $conn) {
        $this->conn = $conn;
    }

    public function guardar(Cita $cita) {
        $stmt = $this->conn->prepare("INSERT INTO citas (cliente_nombre, mascota_nombre, fecha, hora, estado) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$cita->getClienteNombre(), $cita->getMascotaNombre(), $cita->getFecha(), $cita->getHora(), $cita->getEstado()]);
        return $cita->toArray();
    }

    public function buscarPorId($id) {
        $stmt = $this->conn->prepare("SELECT * FROM citas WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar(Cita $cita) {
        $stmt = $this->conn->prepare("UPDATE citas SET estado = ? WHERE id = ?");
        $stmt->execute([$cita->getEstado(), $cita->getId()]);
    }

    public function listar() {
        $stmt = $this->conn->query("SELECT * FROM citas");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
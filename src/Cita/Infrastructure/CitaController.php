<?php
namespace App\Cita\Infrastructure;

use App\Cita\Domain\GestionCita;
class CitaController {
    private $gestionCitas;

    public function __construct(GestionCita $gestionCitas) {
        $this->gestionCitas = $gestionCitas;
    }

    public function crear($data) {
        return $this->gestionCitas->crear($data['cliente_nombre'], $data['mascota_nombre'], $data['fecha'], $data['hora']);
    }

    public function confirmar($id) {
        return $this->gestionCitas->confirmar($id);
    }

    public function cancelar($id) {
        return $this->gestionCitas->cancelar($id);
    }

    public function listar() {
        return $this->gestionCitas->listar();
    }
}
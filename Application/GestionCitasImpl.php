<?php
namespace Application;

use Domain\GestionCitas;
use Domain\RepositorioCitas;
use Domain\Cita;

class GestionCitasImpl implements GestionCitas {
    private $repositorio;

    public function __construct(RepositorioCitas $repositorio) {
        $this->repositorio = $repositorio;
    }

    public function crear($clienteNombre, $mascotaNombre, $fecha, $hora) {
        $cita = new Cita($clienteNombre, $mascotaNombre, $fecha, $hora);
        return $this->repositorio->guardar($cita);
    }

    public function confirmar($id) {
        $data = $this->repositorio->buscarPorId($id);
        if (!$data) throw new \Exception("Cita no encontrada");
        
        $cita = new Cita($data['cliente_nombre'], $data['mascota_nombre'], $data['fecha'], $data['hora'], $data['id']);
        $cita->confirmar();
        $this->repositorio->actualizar($cita);
        return $cita->toArray();
    }

    public function cancelar($id) {
        $data = $this->repositorio->buscarPorId($id);
        if (!$data) throw new \Exception("Cita no encontrada");
        
        $cita = new Cita($data['cliente_nombre'], $data['mascota_nombre'], $data['fecha'], $data['hora'], $data['id']);
        $cita->cancelar();
        $this->repositorio->actualizar($cita);
        return $cita->toArray();
    }

    public function listar() {
        return $this->repositorio->listar();
    }
}
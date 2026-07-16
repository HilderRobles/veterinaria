<?php
namespace App\Cita\Application;

use App\Cita\Domain\GestionCita;
use App\Cita\Domain\RepositorioCita;
use App\Cita\Domain\Cita;

class GestionCitaImpl implements GestionCita {
    private $repositorio;

    public function __construct(RepositorioCita $repositorio) {
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
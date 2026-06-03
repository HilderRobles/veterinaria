<?php
namespace App\Cita\Domain;

interface RepositorioCita {
    public function guardar(Cita $cita);
    public function buscarPorId($id);
    public function actualizar(Cita $cita);
    public function listar();
}
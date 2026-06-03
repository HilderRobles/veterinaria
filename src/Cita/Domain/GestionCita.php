<?php
namespace App\Cita\Domain;

interface GestionCita {
    public function crear($clienteNombre, $mascotaNombre, $fecha, $hora);
    public function confirmar($id);
    public function cancelar($id);
    public function listar();
}
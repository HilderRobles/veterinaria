<?php

namespace App\Cliente\Aplicacion;

use App\Cliente\Dominio\RepositorioCliente;
use App\Cliente\Dominio\ObjetoValor\ClienteId;

class EliminarCliente {
    public function __construct(
        private RepositorioCliente $repositorio
    ) {}

    public function ejecutar(int $clienteId): void {
        $id = new ClienteId($clienteId);
        $cliente = $this->repositorio->buscarPorId($id);

        if ($cliente === null) {
            // 💡 CORRECCIÓN: Cambiar "propietario" por "cliente"
            throw new \DomainException("No se puede eliminar: El cliente no existe.");
        }

        $this->repositorio->eliminar($id);
    }
}
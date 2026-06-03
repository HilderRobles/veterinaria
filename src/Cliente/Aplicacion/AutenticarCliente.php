<?php

namespace App\Cliente\Aplicacion;

use App\Cliente\Dominio\RepositorioCliente;
use App\Cliente\Dominio\ObjetoValor\CorreoElectronico;

class AutenticarCliente {
    public function __construct(
        private RepositorioCliente $repositorio
    ) {}

    public function ejecutar(AutenticarClientePeticion $peticion): array {
        $correo = new CorreoElectronico($peticion->correoElectronico);
        $cliente = $this->repositorio->buscarPorCorreoElectronico($correo);

        if ($cliente === null || !$cliente->obtenerContrasena()->verificar($peticion->contrasenaPlana)) {
            throw new \RuntimeException("Las credenciales de acceso son incorrectas.");
        }

        return $cliente->mapearAArreglo();
    }
}
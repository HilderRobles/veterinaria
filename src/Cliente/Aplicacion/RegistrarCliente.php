<?php

namespace App\Cliente\Aplicacion;

use App\Cliente\Dominio\Cliente;
use App\Cliente\Dominio\RepositorioCliente;
use App\Cliente\Dominio\ObjetoValor\CorreoElectronico;
use App\Cliente\Dominio\ObjetoValor\Telefono;

class RegistrarCliente {
    public function __construct(
        private RepositorioCliente $repositorio,
        private CifradorContrasena $cifrador
    ) {}

    public function ejecutar(RegistrarClientePeticion $peticion): void {
        $correo = new CorreoElectronico($peticion->correoElectronico);
        $telefono = new Telefono($peticion->telefono);

        if ($this->repositorio->buscarPorCorreoElectronico($correo) !== null) {
            throw new \DomainException("El correo electrónico ya se encuentra registrado.");
        }

        $contrasenaCifrada = $this->cifrador->cifrar($peticion->contrasenaPlana);
        $cliente = new Cliente($peticion->nombre, $correo, $telefono, $contrasenaCifrada);

        $this->repositorio->guardar($cliente);
    }
}
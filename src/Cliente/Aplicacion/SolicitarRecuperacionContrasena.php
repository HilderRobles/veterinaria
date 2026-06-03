<?php

namespace App\Cliente\Aplicacion;

use App\Cliente\Dominio\RepositorioCliente;
use App\Cliente\Dominio\ObjetoValor\CorreoElectronico;

class SolicitarRecuperacionContrasena {
    public function __construct(
        private RepositorioCliente $repositorio,
        private EnviadorNotificaciones $enviadorNotificaciones
    ) {}

    public function ejecutar(RecuperarContrasenaPeticion $peticion): void {
        $correo = new CorreoElectronico($peticion->correoElectronico);
        $cliente = $this->repositorio->buscarPorCorreoElectronico($correo);

        if ($cliente === null) {
            return; 
        }

        $enlaceRecuperacion = "https://veterinaria.com/restablecer-clave?token=tokenSeguro123";
        $this->enviadorNotificaciones->enviarCorreoRestablecimiento($correo, $enlaceRecuperacion);
    }
}
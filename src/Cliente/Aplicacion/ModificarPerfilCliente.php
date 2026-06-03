<?php

namespace App\Cliente\Aplicacion;

use App\Cliente\Dominio\RepositorioCliente;
use App\Cliente\Dominio\ObjetoValor\ClienteId;
use App\Cliente\Dominio\ObjetoValor\CorreoElectronico;
use App\Cliente\Dominio\ObjetoValor\Telefono;

class ModificarPerfilCliente {
    public function __construct(
        private RepositorioCliente $repositorio,
        private EnviadorNotificaciones $enviadorNotificaciones
    ) {}

    public function ejecutar(ModificarPerfilClientePeticion $peticion): void {
        $clienteId = new ClienteId($peticion->clienteId);
        $cliente = $this->repositorio->buscarPorId($clienteId);

        if ($cliente === null) {
            throw new \DomainException("El propietario solicitado no existe en el sistema.");
        }

        $correoOriginal = $cliente->obtenerCorreoElectronico();
        $telefonoOriginal = $cliente->obtenerTelefono();

        $nombreFinal = $peticion->nuevoNombre !== null ? $peticion->nuevoNombre : $cliente->obtenerNombre();
        $correoFinal = $peticion->nuevoCorreo !== null ? new CorreoElectronico($peticion->nuevoCorreo) : $correoOriginal;
        $telefonoFinal = $peticion->nuevoTelefono !== null ? new Telefono($peticion->nuevoTelefono) : $telefonoOriginal;

        if ($peticion->nuevoCorreo !== null && $correoOriginal->valor() !== $correoFinal->valor()) {
            if ($this->repositorio->buscarPorCorreoElectronico($correoFinal) !== null) {
                throw new \DomainException("El nuevo correo electrónico ya está registrado por otro propietario.");
            }
        }

        $this->repositorio->actualizarDatosContacto($clienteId, $nombreFinal, $correoFinal, $telefonoFinal);

        if ($correoOriginal->valor() !== $correoFinal->valor()) {
            $this->enviadorNotificaciones->enviarSmsAlerta(
                $telefonoOriginal,
                "Seguridad: El correo de tu cuenta veterinaria ha sido cambiado a: {$correoFinal->valor()}."
            );
        }

        if ($telefonoOriginal->valor() !== $telefonoFinal->valor()) {
            $this->enviadorNotificaciones->enviarEmailAlerta(
                $correoOriginal,
                "Alerta: El teléfono de tu perfil ha sido modificado al: {$telefonoFinal->valor()}."
            );
        }
    }
}
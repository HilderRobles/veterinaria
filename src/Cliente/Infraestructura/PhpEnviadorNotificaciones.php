<?php

namespace App\Cliente\Infraestructura;

use App\Cliente\Aplicacion\EnviadorNotificaciones;
use App\Cliente\Dominio\ObjetoValor\CorreoElectronico;
use App\Cliente\Dominio\ObjetoValor\Telefono;

class PhpEnviadorNotificaciones implements EnviadorNotificaciones {
    public function enviarCorreoRestablecimiento(CorreoElectronico $correo, string $enlace): void {
        error_log("Enlace enviado a {$correo->valor()}: {$enlace}");
    }
    public function enviarSmsAlerta(Telefono $telefono, string $mensaje): void {
        error_log("SMS a {$telefono->valor()}: {$mensaje}");
    }
    public function enviarEmailAlerta(CorreoElectronico $correo, string $mensaje): void {
        error_log("Email de alerta a {$correo->valor()}: {$mensaje}");
    }
}
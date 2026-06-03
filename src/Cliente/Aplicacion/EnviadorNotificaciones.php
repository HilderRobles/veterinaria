<?php

namespace App\Cliente\Aplicacion;

use App\Cliente\Dominio\ObjetoValor\CorreoElectronico;
use App\Cliente\Dominio\ObjetoValor\Telefono;

interface EnviadorNotificaciones {
    public function enviarCorreoRestablecimiento(CorreoElectronico $correo, string $enlace): void;
    public function enviarSmsAlerta(Telefono $telefono, string $mensaje): void;
    public function enviarEmailAlerta(CorreoElectronico $correo, string $mensaje): void;
}
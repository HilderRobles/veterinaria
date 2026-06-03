<?php

namespace App\Cliente\Aplicacion;

class AutenticarClientePeticion {
    public function __construct(
        public string $correoElectronico,
        public string $contrasenaPlana
    ) {}
}
<?php

namespace App\Cliente\Aplicacion;

class RegistrarClientePeticion {
    public function __construct(
        public string $nombre,
        public string $correoElectronico,
        public string $telefono,
        public string $contrasenaPlana
    ) {}
}
<?php

namespace App\Cliente\Aplicacion;

class RecuperarContrasenaPeticion {
    public function __construct(
        public string $correoElectronico
    ) {}
}
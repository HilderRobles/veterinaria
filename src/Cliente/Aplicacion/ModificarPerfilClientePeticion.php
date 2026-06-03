<?php

namespace App\Cliente\Aplicacion;

class ModificarPerfilClientePeticion {
    public function __construct(
        public int $clienteId,
        public ?string $nuevoNombre = null,
        public ?string $nuevoCorreo = null,
        public ?string $nuevoTelefono = null
    ) {}
}
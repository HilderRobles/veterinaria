<?php

namespace App\Cliente\Infraestructura;

use App\Cliente\Aplicacion\CifradorContrasena;
use App\Cliente\Dominio\ObjetoValor\Contrasena;

class PhpCifradorContrasena implements CifradorContrasena {
    public function cifrar(string $contrasenaPlana): Contrasena {
        $hash = password_hash($contrasenaPlana, PASSWORD_BCRYPT);
        return new Contrasena($hash);
    }
}
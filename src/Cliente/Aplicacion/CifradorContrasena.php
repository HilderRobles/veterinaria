<?php

namespace App\Cliente\Aplicacion;

use App\Cliente\Dominio\ObjetoValor\Contrasena;

interface CifradorContrasena {
    public function cifrar(string $contrasenaPlana): Contrasena;
}
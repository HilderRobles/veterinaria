<?php

namespace App\Cliente\Dominio\Repositorio;

use App\Cliente\Dominio\Cliente;
use App\Cliente\Dominio\ObjetoValor\ClienteId;
use App\Cliente\Dominio\ObjetoValor\CorreoElectronico;

interface RepositorioCliente {
    public function guardar(Cliente $cliente): void;
    public function buscarPorId(ClienteId $id): ?Cliente;
    public function buscarPorCorreoElectronico(CorreoElectronico $correoElectronico): ?Cliente;

    // NADA de PHPDoc. Tipado de PHP 8 puro, robusto y elegante.
    public function obtenerListadoParaAdministrador(): ColeccionClientesTabla;
}
<?php

namespace App\Cliente\Aplicacion;

use App\Cliente\Dominio\RepositorioCliente;
use App\Cliente\Dominio\Cliente;

class ListarCliente {
    private RepositorioCliente $repositorio;

    public function __construct(RepositorioCliente $repositorio) {
        $this->repositorio = $repositorio;
    }

    public function ejecutar(): array {
        $clientesDominio = $this->repositorio->buscarTodos();

        return array_map(function (Cliente $cliente) {
            return [
                // Ajusta estos métodos según cómo se llamen los getters reales de tu entidad Cliente
                'id_cliente' => method_exists($cliente, 'obtenerId') ? $cliente->obtenerId()->valor() : null, 
                'nombre'     => $cliente->obtenerNombre(),
                'email'      => $cliente->obtenerCorreoElectronico()->valor(),
                'telefono'   => $cliente->obtenerTelefono()->valor(),
                'rol'        => method_exists($cliente, 'obtenerRol') ? $cliente->obtenerRol() : 'cliente'
            ];
        }, $clientesDominio);
    }
}
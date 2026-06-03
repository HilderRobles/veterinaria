<?php

// 💡 Este namespace coincide exactamente con la ruta de carpetas
namespace App\Cliente\Dominio;

use App\Cliente\Dominio\Cliente;
use App\Cliente\Dominio\ObjetoValor\ClienteId;
use App\Cliente\Dominio\ObjetoValor\CorreoElectronico;
use App\Cliente\Dominio\ObjetoValor\Telefono;

interface RepositorioCliente {
    public function guardar(Cliente $cliente): void;
    public function buscarPorId(ClienteId $id): ?Cliente;
    public function buscarPorCorreoElectronico(CorreoElectronico $correoElectronico): ?Cliente;
    public function actualizarDatosContacto(ClienteId $id, string $nombre, CorreoElectronico $correo, Telefono $telefono): void;
    public function eliminar(ClienteId $id): void;
}
<?php

namespace App\Cliente\Infraestructura;

use App\Cliente\Dominio\RepositorioCliente;
use App\Cliente\Dominio\Cliente;
use App\Cliente\Dominio\ObjetoValor\ClienteId;
use App\Cliente\Dominio\ObjetoValor\CorreoElectronico;
use App\Cliente\Dominio\ObjetoValor\Telefono;
use App\Cliente\Dominio\ObjetoValor\Contrasena;

class PdoRepositorioCliente implements RepositorioCliente {
    
    public function __construct(private \PDO $pdo) {}

    public function guardar(Cliente $cliente): void {
        $stmt = $this->pdo->prepare("
            INSERT INTO clientes (nombre, email, telefono, password_hash, rol) 
            VALUES (:nombre, :email, :telefono, :password_hash, :rol)
        ");
        
        $stmt->execute([
            'nombre'        => $cliente->obtenerNombre(),
            'email'         => $cliente->obtenerCorreoElectronico()->valor(),
            'telefono'      => $cliente->obtenerTelefono()->valor(),
            'password_hash' => $cliente->obtenerContrasena()->valor(),
            'rol'           => method_exists($cliente, 'obtenerRol') ? $cliente->obtenerRol() : 'cliente'
        ]);
    }

    public function buscarPorId(ClienteId $id): ?Cliente {
        $stmt = $this->pdo->prepare("
            SELECT id_cliente, nombre, email, telefono, password_hash, rol 
            FROM clientes 
            WHERE id_cliente = :id_cliente
        ");
        $stmt->execute(['id_cliente' => $id->valor()]);
        
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$row) return null;

        return new Cliente(
            $row['nombre'],
            new CorreoElectronico($row['email']),
            new Telefono($row['telefono']),
            new Contrasena($row['password_hash']),
            $row['rol'],
            new ClienteId((int)$row['id_cliente']), // Casteo obligatorio para cumplir con tu Objeto de Valor

        );
    }

    public function buscarPorCorreoElectronico(CorreoElectronico $correoElectronico): ?Cliente {
        $stmt = $this->pdo->prepare("
            SELECT id_cliente, nombre, email, telefono, password_hash, rol 
            FROM clientes 
            WHERE email = :email
        ");
        $stmt->execute(['email' => $correoElectronico->valor()]);
        
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$row) return null;

        return new Cliente(
            $row['nombre'],
            new CorreoElectronico($row['email']),
            new Telefono($row['telefono']),
            new Contrasena($row['password_hash']),
            $row['rol'],
            new ClienteId((int)$row['id_cliente']), // Casteo obligatorio para cumplir con tu Objeto de Valor
        );
    }

    public function actualizarDatosContacto(ClienteId $id, string $nombre, CorreoElectronico $correo, Telefono $telefono): void {
        $stmt = $this->pdo->prepare("
            UPDATE clientes 
            SET nombre = :nombre, email = :email, telefono = :telefono 
            WHERE id_cliente = :id_cliente
        ");
        
        $stmt->execute([
            'id_cliente' => $id->valor(),
            'nombre'     => $nombre,
            'email'      => $correo->valor(),
            'telefono'   => $telefono->valor()
        ]);
    }

    public function eliminar(ClienteId $id): void {
        $stmt = $this->pdo->prepare("DELETE FROM clientes WHERE id_cliente = :id_cliente");
        $stmt->execute(['id_cliente' => $id->valor()]);
    }

    public function buscarTodos(): array {
        $stmt = $this->pdo->prepare("
            SELECT id_cliente, nombre, email, telefono, password_hash, rol 
            FROM clientes
        ");
        $stmt->execute();
        
        $clientes = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            // Reconstruimos la Entidad de Dominio respetando el orden de tu constructor
            $clientes[] = new Cliente(
                $row['nombre'],
                new CorreoElectronico($row['email']),
                new Telefono($row['telefono']),
                new Contrasena($row['password_hash']),
                $row['rol'],
                new ClienteId((int)$row['id_cliente']) // Casteo obligatorio para cumplir con tu Objeto de Valor
            );
        }
        
        return $clientes;
    }

}
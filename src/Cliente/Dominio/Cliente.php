<?php

namespace App\Cliente\Dominio;

use App\Cliente\Dominio\ObjetoValor\ClienteId;
use App\Cliente\Dominio\ObjetoValor\CorreoElectronico;
use App\Cliente\Dominio\ObjetoValor\Telefono;
use App\Cliente\Dominio\ObjetoValor\Contrasena;

class Cliente {
    private ?ClienteId $id;
    private string $nombre;
    private CorreoElectronico $correoElectronico;
    private Telefono $telefono;
    private Contrasena $contrasena;
    private string $rol;

    public function __construct(
        string $nombre,
        CorreoElectronico $correoElectronico,
        Telefono $telefono,
        Contrasena $contrasena,
        string $rol = 'cliente',
        ?ClienteId $id = null
    ) {
        if (empty(trim($nombre))) {
            throw new \DomainException("El nombre del cliente no puede estar vacío.");
        }
        
        $this->nombre = $nombre;
        $this->correoElectronico = $correoElectronico;
        $this->telefono = $telefono;
        $this->contrasena = $contrasena;
        $this->id = $id;
        $this->establecerRol($rol);
    }

    // 🎯 Mutaciones de estado y Reglas de Negocio
    public function promoverAVeterinario(): void {
        $this->rol = 'veterinario';
    }

    public function promoverAAdministrador(): void {
        $this->rol = 'admin';
    }

    private function establecerRol(string $rol): void {
        $rolesPermitidos = ['cliente', 'admin', 'veterinario'];
        if (!in_array($rol, $rolesPermitidos)) {
            throw new \DomainException("El rol '{$rol}' no está permitido en la veterinaria.");
        }
        $this->rol = $rol;
    }

    // Getters estrictamente tipados en castellano
    public function obtenerId(): ?ClienteId { return $this->id; }
    public function obtenerNombre(): string { return $this->nombre; }
    public function obtenerCorreoElectronico(): CorreoElectronico { return $this->correoElectronico; }
    public function obtenerTelefono(): Telefono { return $this->telefono; }
    public function obtenerContrasena(): Contrasena { return $this->contrasena; }
    public function obtenerRol(): string { return $this->rol; }

    public function mapearAArreglo(): array {
        return [
            'id' => $this->id ? $this->id->valor() : null,
            'nombre' => $this->nombre,
            'correo_electronico' => $this->correoElectronico->valor(),
            'telefono' => $this->telefono->valor(),
            'rol' => $this->rol
        ];
    }
}
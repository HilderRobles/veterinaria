<?php
namespace Domain;

class Usuario {
    private $id;
    private $nombre;
    private $email;
    private $telefono;
    private $passwordHash;
    private $rol;

    public function __construct(
        $nombre,
        $email,
        $telefono,
        $passwordHash,
        $rol,
        $id = null
    ) {

        if (empty($nombre)) {
            throw new \Exception("Nombre requerido");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \Exception("Email inválido");
        }

        $this->nombre = $nombre;
        $this->email = $email;
        $this->telefono = $telefono;
        $this->passwordHash = $passwordHash;
        $this->rol = $rol;
        $this->id = $id;
    }

    public function getId() { return $this->id; }
    public function getNombre() { return $this->nombre; }
    public function getEmail() { return $this->email; }
    public function getTelefono() { return $this->telefono; }
    public function getPasswordHash() { return $this->passwordHash; }
    public function getRol() { return $this->rol; }
}
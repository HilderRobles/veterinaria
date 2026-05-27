<?php
namespace Domain;

class Mascota {

    private $id;
    private $idUsuario;
    private $nombre;
    private $especie;
    private $raza;
    private $edad;
    private $peso;
    private $sexo;

    public function __construct(
        $idUsuario,
        $nombre,
        $especie,
        $raza,
        $edad,
        $peso,
        $sexo,
        $id = null
    ) {

        if (empty($nombre)) {
            throw new \Exception("Nombre requerido");
        }

        $this->idUsuario = $idUsuario;
        $this->nombre = $nombre;
        $this->especie = $especie;
        $this->raza = $raza;
        $this->edad = $edad;
        $this->peso = $peso;
        $this->sexo = $sexo;
        $this->id = $id;
    }

    public function getId() { return $this->id; }
    public function getNombre() { return $this->nombre; }
}
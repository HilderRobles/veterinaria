<?php
namespace Domain;

class HistorialClinico {

    private $id;
    private $idMascota;
    private $idCita;
    private $diagnostico;
    private $observaciones;

    public function __construct(
        $idMascota,
        $idCita,
        $diagnostico,
        $observaciones,
        $id = null
    ) {

        $this->idMascota = $idMascota;
        $this->idCita = $idCita;
        $this->diagnostico = $diagnostico;
        $this->observaciones = $observaciones;
        $this->id = $id;
    }
}
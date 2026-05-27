<?php
namespace Domain;

class Venta {

    private $id;
    private $idUsuario;
    private $montoTotal;

    public function __construct(
        $idUsuario,
        $montoTotal,
        $id = null
    ) {

        if ($montoTotal <= 0) {
            throw new \Exception("Monto inválido");
        }

        $this->idUsuario = $idUsuario;
        $this->montoTotal = $montoTotal;
        $this->id = $id;
    }
}
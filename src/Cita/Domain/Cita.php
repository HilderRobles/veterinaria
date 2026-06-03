<?php
namespace App\Cita\Domain;
class Cita {
    private $id;
    private $clienteNombre;
    private $mascotaNombre;
    private $fecha;
    private $hora;
    private $estado;

    public function __construct($clienteNombre, $mascotaNombre, $fecha, $hora, $id = null) {
        if (empty($clienteNombre)) throw new \Exception("Cliente requerido");
        if (empty($mascotaNombre)) throw new \Exception("Mascota requerida");
        
        $this->clienteNombre = $clienteNombre;
        $this->mascotaNombre = $mascotaNombre;
        $this->fecha = $fecha;
        $this->hora = $hora;
        $this->id = $id;
        $this->estado = 'pendiente';
    }

    public function confirmar() {
        if ($this->estado === 'cancelada') throw new \Exception("No se puede confirmar cita cancelada");
        $this->estado = 'confirmada';
    }

    public function cancelar() {
        if ($this->estado === 'confirmada') throw new \Exception("No se puede cancelar cita confirmada");
        $this->estado = 'cancelada';
    }

    public function getId() { return $this->id; }
    public function getClienteNombre() { return $this->clienteNombre; }
    public function getMascotaNombre() { return $this->mascotaNombre; }
    public function getFecha() { return $this->fecha; }
    public function getHora() { return $this->hora; }
    public function getEstado() { return $this->estado; }
    
    public function toArray() {
        return [
            'id' => $this->id,
            'cliente_nombre' => $this->clienteNombre,
            'mascota_nombre' => $this->mascotaNombre,
            'fecha' => $this->fecha,
            'hora' => $this->hora,
            'estado' => $this->estado
        ];
    }
}
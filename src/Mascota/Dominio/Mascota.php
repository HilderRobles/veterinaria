<?php

declare(strict_types=1);

namespace App\Mascota\Dominio;

use App\Mascota\Dominio\ObjetoValor\MascotaId;
use App\Cliente\Dominio\ObjetoValor\ClienteId;
use InvalidArgumentException;

final class Mascota
{
    public function __construct(
        private MascotaId $id,
        private ClienteId $clienteId,
        private string $nombre,
        private string $especie,
        private float $peso
    ) {
        $this->validarNombre($nombre);
        $this->validarPeso($peso);
    }

    public static function registrar(MascotaId $id, ClienteId $clienteId, string $nombre, string $especie, float $peso): self
    {
        return new self($id, $clienteId, $nombre, $especie, $peso);
    }

    private function validarNombre(string $nombre): void
    {
        if (trim($nombre) === '') {
            throw new InvalidArgumentException("El nombre de la mascota no puede estar vacío.");
        }
    }

    private function validarPeso(float $peso): void
    {
        if ($peso <= 0) {
            throw new InvalidArgumentException("El peso de la mascota debe ser mayor a 0.");
        }
    }

    public function obtenerId(): MascotaId
    {
        return $this->id;
    }

    public function obtenerClienteId(): ClienteId
    {
        return $this->clienteId;
    }

    public function obtenerNombre(): string
    {
        return $this->nombre;
    }

    public function obtenerEspecie(): string
    {
        return $this->especie;
    }

    public function obtenerPeso(): float
    {
        return $this->peso;
    }
}
<?php

declare(strict_types=1);

namespace App\Cita\Dominio;

interface RepositorioCita
{
    public function guardar(Cita $cita): void;
    public function actualizar(Cita $cita): void;
    public function buscarPorId(int $id): ?Cita;
    public function listarTodas(): array;
}
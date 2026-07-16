<?php

declare(strict_types=1);

namespace App\Mascota\Infraestructura;

use App\Mascota\Aplicacion\RegistrarMascota;
use App\Mascota\Aplicacion\RegistrarMascotaPeticion;
use InvalidArgumentException;
use Exception;

final class MascotaController
{
    public function __construct(private RegistrarMascota $registrarMascotaUseCase)
    {
    }

    public function registrar(array $datosRequest): array
    {
        try {
            // Empaquetamos los datos crudos en un DTO
            $peticion = new RegistrarMascotaPeticion(
                (int) $datosRequest['id'],
                (int) $datosRequest['cliente_id'],
                $datosRequest['nombre'],
                $datosRequest['especie'],
                (float) $datosRequest['peso']
            );

            // Invocamos la capa de aplicación (Caso de Uso)
            $this->registrarMascotaUseCase->ejecutar($peticion);

            return [
                'status' => 201,
                'mensaje' => 'Mascota registrada exitosamente.'
            ];
            
        } catch (InvalidArgumentException | Exception $e) {
            // Capturamos cualquier regla de negocio rota y devolvemos un 400 Bad Request
            return [
                'status' => 400,
                'error' => $e->getMessage()
            ];
        }
    }
}
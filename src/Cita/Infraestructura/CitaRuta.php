<?php
declare(strict_types=1);
namespace App\Cita\Infraestructura;

use App\Cita\Aplicacion\{
    AgendarCita, AgendarCitaPeticion, 
    ConfirmarCita, AtenderCita, CancelarCita, ListarCita
};
use PDO;

class CitaRuta 
{
    public static function despachar(string $metodo, string $path, array $input, PDO $pdo): array 
    {
        $repositorio = new PdoRepositorioCita($pdo);
        $notificador = new WhatsAppNotificador(); // 📲 Instanciamos nuestra simulación de WhatsApp

        // GET: LISTAR CITAS
        if ($metodo === 'GET' && $path === '/api/citas') {
            $citas = (new ListarCita($repositorio))->ejecutar();
            
            http_response_code(200);
            echo json_encode($citas);
            return $citas;
        }

        // POST: AGENDAR CITA
        if ($metodo === 'POST' && $path === '/api/citas') {
            try {
                $peticion = new AgendarCitaPeticion(
                    (int)($input['id_cliente'] ?? 0),
                    (int)($input['id_mascota'] ?? 0),
                    (int)($input['id_servicio'] ?? 0),
                    $input['fecha'] ?? '',
                    $input['hora'] ?? '',
                    $input['motivo'] ?? ''
                );
                
                // Le pasamos el Repositorio Y el Notificador
                (new AgendarCita($repositorio, $notificador))->ejecutar($peticion);
                
                http_response_code(201);
                $respuesta = ['mensaje' => 'Cita agendada correctamente.'];
                echo json_encode($respuesta);
                return $respuesta;
            } catch (\Exception $e) {
                http_response_code(400);
                $error = ['error' => $e->getMessage()];
                echo json_encode($error);
                return $error;
            }
        }

        // PUT: CONFIRMAR CITA
        if ($metodo === 'PUT' && $path === '/api/citas/confirmar') {
            try {
                $id = (int)($input['id_cita'] ?? 0);
                
                // Le pasamos el Repositorio Y el Notificador
                (new ConfirmarCita($repositorio, $notificador))->ejecutar($id);
                
                http_response_code(200);
                $respuesta = ['mensaje' => 'Cita confirmada con éxito.'];
                echo json_encode($respuesta);
                return $respuesta;
            } catch (\Exception $e) {
                http_response_code(400);
                $error = ['error' => $e->getMessage()];
                echo json_encode($error);
                return $error;
            }
        }

        // PUT: ATENDER CITA
        if ($metodo === 'PUT' && $path === '/api/citas/atender') {
            try {
                $id = (int)($input['id_cita'] ?? 0);
                (new AtenderCita($repositorio))->ejecutar($id);
                
                http_response_code(200);
                $respuesta = ['mensaje' => 'La mascota ha sido atendida. Cita finalizada con éxito.'];
                echo json_encode($respuesta);
                return $respuesta;
            } catch (\Exception $e) {
                http_response_code(400);
                $error = ['error' => $e->getMessage()];
                echo json_encode($error);
                return $error;
            }
        }

        // PUT: CANCELAR CITA
        if ($metodo === 'PUT' && $path === '/api/citas/cancelar') {
            try {
                $id = (int)($input['id_cita'] ?? 0);
                
                // Cancelar solo requiere repositorio por ahora
                (new CancelarCita($repositorio))->ejecutar($id);
                
                http_response_code(200);
                $respuesta = ['mensaje' => 'Cita cancelada con éxito.'];
                echo json_encode($respuesta);
                return $respuesta;
            } catch (\Exception $e) {
                http_response_code(400);
                $error = ['error' => $e->getMessage()];
                echo json_encode($error);
                return $error;
            }
        }

        http_response_code(404);
        $error = ['error' => 'Ruta de cita no encontrada.'];
        echo json_encode($error);
        return $error;
    }
}
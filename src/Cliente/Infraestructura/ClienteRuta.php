<?php

namespace App\Cliente\Infraestructura;

use App\Cliente\Aplicacion\{
    RegistrarCliente, RegistrarClientePeticion, 
    AutenticarCliente, AutenticarClientePeticion, 
    ModificarPerfilCliente, ModificarPerfilClientePeticion, 
    EliminarCliente, SolicitarRecuperacionContrasena, 
    RecuperarContrasenaPeticion,ListarCliente
};

class ClienteRuta {
    /**
     * Despacha la ruta de clientes.
     * En lugar de cortar la ejecución con exit;, retorna un array con el resultado 
     * para que sea 100% testeable y compatible con producción.
     */
public static function despachar(string $metodo, string $path, array $input, \PDO $pdo): array {
        $repositorio = new PdoRepositorioCliente($pdo);
        $cifrador    = new PhpCifradorContrasena();
        $enviador    = new PhpEnviadorNotificaciones();

        // 🟢 NUEVA RUTA: MOSTRAR / LISTAR TODOS LOS CLIENTES
        if ($metodo === 'GET' && $path === '/api/clientes') {
            $clientes = (new ListarCliente($repositorio))->ejecutar();
            
            http_response_code(200);
            echo json_encode($clientes);
            return $clientes;
        }

        if ($metodo === 'POST' && $path === '/api/clientes') {
            $peticion = new RegistrarClientePeticion(
                $input['nombre'] ?? '', $input['email'] ?? '', 
                $input['telefono'] ?? '', $input['password'] ?? ''
            );
            (new RegistrarCliente($repositorio, $cifrador))->ejecutar($peticion);
            
            http_response_code(201);
            $respuesta = ['mensaje' => 'Cliente registrado exitosamente.'];
            echo json_encode($respuesta);
            return $respuesta; // ⬅️ Salida limpia que permite la cobertura de Xdebug
        }

        if ($metodo === 'POST' && $path === '/api/clientes/login') {
            $peticion = new AutenticarClientePeticion($input['email'] ?? '', $input['password'] ?? '');
            $perfil = (new AutenticarCliente($repositorio))->ejecutar($peticion);
            
            http_response_code(200);
            $respuesta = ['mensaje' => 'Login correcto.', 'cliente' => $perfil];
            echo json_encode($respuesta);
            return $respuesta;
        }

        if ($metodo === 'PUT' && $path === '/api/clientes') {
            $peticion = new ModificarPerfilClientePeticion(
                (int)($input['id_cliente'] ?? 0), $input['nombre'] ?? null, 
                $input['email'] ?? null, $input['telefono'] ?? null
            );
            (new ModificarPerfilCliente($repositorio, $enviador))->ejecutar($peticion);
            
            http_response_code(200);
            $respuesta = ['mensaje' => 'Perfil actualizado.'];
            echo json_encode($respuesta);
            return $respuesta;
        }

        if ($metodo === 'DELETE' && $path === '/api/clientes') {
            $id = (int)($input['id_cliente'] ?? ($_GET['id'] ?? 0));
            (new EliminarCliente($repositorio))->ejecutar($id);
            
            http_response_code(200);
            $respuesta = ['mensaje' => 'Cliente eliminado.'];
            echo json_encode($respuesta);
            return $respuesta;
        }

        if ($metodo === 'POST' && $path === '/api/clientes/recuperar') {
            $peticion = new RecuperarContrasenaPeticion($input['email'] ?? '');
            (new SolicitarRecuperacionContrasena($repositorio, $enviador))->ejecutar($peticion);
            
            http_response_code(200);
            $respuesta = ['mensaje' => 'Proceso de recuperación iniciado.'];
            echo json_encode($respuesta);
            return $respuesta;
        }

        http_response_code(404);
        $error = ['error' => 'Ruta de cliente no encontrada.'];
        echo json_encode($error);
        return $error;
    }
}
<?php

namespace Tests\Cliente\Infraestructura;

use App\Cliente\Infraestructura\ClienteRuta; 
use App\Cliente\Infraestructura\PdoRepositorioCliente;
use App\Cliente\Infraestructura\PhpCifradorContrasena;
use App\Cliente\Infraestructura\PhpEnviadorNotificaciones;
use App\Cliente\Dominio\Cliente;
use App\Cliente\Dominio\ObjetoValor\ClienteId;
use App\Cliente\Dominio\ObjetoValor\CorreoElectronico;
use App\Cliente\Dominio\ObjetoValor\Telefono;
use App\Cliente\Dominio\ObjetoValor\Contrasena;
use App\Cliente\Aplicacion\RegistrarCliente;
use App\Cliente\Aplicacion\RegistrarClientePeticion;
use App\Cliente\Aplicacion\ModificarPerfilCliente;
use App\Cliente\Aplicacion\ModificarPerfilClientePeticion;
use App\Cliente\Aplicacion\SolicitarRecuperacionContrasena;
use App\Cliente\Aplicacion\RecuperarContrasenaPeticion;
use App\Cliente\Aplicacion\AutenticarCliente;
use App\Cliente\Aplicacion\AutenticarClientePeticion;
use App\Cliente\Aplicacion\EliminarCliente;
use App\Cliente\Aplicacion\ListarCliente; // 🟢 Corregido en singular como tu software

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(ClienteRuta::class)]
#[UsesClass(PdoRepositorioCliente::class)]
#[UsesClass(PhpCifradorContrasena::class)]
#[UsesClass(PhpEnviadorNotificaciones::class)]
#[UsesClass(Cliente::class)]
#[UsesClass(ClienteId::class)]
#[UsesClass(CorreoElectronico::class)]
#[UsesClass(Telefono::class)]
#[UsesClass(Contrasena::class)]
#[UsesClass(RegistrarCliente::class)]
#[UsesClass(RegistrarClientePeticion::class)]
#[UsesClass(ModificarPerfilCliente::class)]
#[UsesClass(ModificarPerfilClientePeticion::class)]
#[UsesClass(SolicitarRecuperacionContrasena::class)]
#[UsesClass(RecuperarContrasenaPeticion::class)]
#[UsesClass(AutenticarCliente::class)]
#[UsesClass(AutenticarClientePeticion::class)]
#[UsesClass(EliminarCliente::class)]
#[UsesClass(ListarCliente::class)] // 🟢 Objetivo válido de cobertura mapeado correctamente
class ClienteRutaTest extends TestCase {
    private \PDO $pdo;

    protected function setUp(): void {
        $dependencies = require __DIR__ . '/../../../bootstrap.php';
        $this->pdo = $dependencies['database'];
        $this->pdo->beginTransaction();
        
        $this->iniSet('error_log', 'nul');
    }

    protected function tearDown(): void {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }

    public function test_ruta_get_mostrar_clientes_exitosamente(): void {
        $email = 'listar_' . uniqid() . '@test.com';
        (new PdoRepositorioCliente($this->pdo))->guardar(new Cliente(
            "User Listar", 
            new CorreoElectronico($email), 
            new Telefono('911222333'), 
            new Contrasena("hash"),
            "cliente"
        ));

        // Capturamos el echo nativo del json_encode de tu software para no ensuciar la consola
        $this->expectOutputRegex('/.*/'); 
        
        $resultado = ClienteRuta::despachar('GET', '/api/clientes', [], $this->pdo);

        $this->assertIsArray($resultado);
    }

    public function test_ruta_post_registrar_cliente_exitosamente(): void {
        $input = [
            'nombre' => 'Clark Kent', 
            'email' => 'clark_' . uniqid() . '@dailyplanet.com', 
            'telefono' => '955444333', 
            'password' => 'Krypton55'
        ];

        // 🟢 Tu software hace echo de esto. Le avisamos a PHPUnit que lo espere exactamente:
        $this->expectOutputString(json_encode(['mensaje' => 'Cliente registrado exitosamente.']));

        $resultado = ClienteRuta::despachar('POST', '/api/clientes', $input, $this->pdo);
        
        $this->assertIsArray($resultado);
        $this->assertEquals('Cliente registrado exitosamente.', $resultado['mensaje']);
    }

    public function test_ruta_post_login_exitosamente(): void {
        $email = 'login_' . uniqid() . '@test.com';
        (new PdoRepositorioCliente($this->pdo))->guardar(new Cliente(
            "User Test", 
            new CorreoElectronico($email), 
            new Telefono("966555444"), 
            new Contrasena(password_hash("password123", PASSWORD_BCRYPT))
        ));

        // Capturamos el echo dinámico del login
        $this->expectOutputRegex('/Login correcto/');

        $resultado = ClienteRuta::despachar('POST', '/api/clientes/login', ['email' => $email, 'password' => 'password123'], $this->pdo);

        $this->assertIsArray($resultado);
        $this->assertEquals('Login correcto.', $resultado['mensaje']);
    }

    public function test_ruta_put_actualizar_perfil_exitosamente(): void {
        $email = 'put_' . uniqid() . '@test.com';
        $repo = new PdoRepositorioCliente($this->pdo);
        $repo->guardar(new Cliente("User Update", new CorreoElectronico($email), new Telefono("944333222"), new Contrasena("hash")));
        $cliente = $repo->buscarPorCorreoElectronico(new CorreoElectronico($email));

        $input = [
            'id_cliente' => $cliente->obtenerId()->valor(), 
            'nombre' => 'Modificado', 
            'email' => 'n_' . uniqid() . '@test.com', 
            'telefono' => '987654321'
        ];
        
        $this->expectOutputString(json_encode(['mensaje' => 'Perfil actualizado.']));

        $resultado = ClienteRuta::despachar('PUT', '/api/clientes', $input, $this->pdo);

        $this->assertIsArray($resultado);
        $this->assertEquals('Perfil actualizado.', $resultado['mensaje']);
    }

    public function test_ruta_delete_eliminar_cliente_exitosamente(): void {
        $email = 'delete_' . uniqid() . '@test.com';
        $repo = new PdoRepositorioCliente($this->pdo);
        $repo->guardar(new Cliente("User Delete", new CorreoElectronico($email), new Telefono("933222111"), new Contrasena("hash")));
        $cliente = $repo->buscarPorCorreoElectronico(new CorreoElectronico($email));

        $this->expectOutputString(json_encode(['mensaje' => 'Cliente eliminado.']));

        $resultado = ClienteRuta::despachar('DELETE', '/api/clientes', ['id_cliente' => $cliente->obtenerId()->valor()], $this->pdo);

        $this->assertIsArray($resultado);
        $this->assertEquals('Cliente eliminado.', $resultado['mensaje']);
    }

    public function test_ruta_post_recuperar_contrasena_exitosamente(): void {
        $this->expectOutputString(json_encode(['mensaje' => 'Proceso de recuperación iniciado.']));

        $resultado = ClienteRuta::despachar('POST', '/api/clientes/recuperar', ['email' => 'test@midominio.com'], $this->pdo);

        $this->assertIsArray($resultado);
        $this->assertEquals('Proceso de recuperación iniciado.', $resultado['mensaje']);
    }

    public function test_ruta_no_encontrada_devuelve_404(): void {
        $this->expectOutputString(json_encode(['error' => 'Ruta de cliente no encontrada.']));

        $resultado = ClienteRuta::despachar('GET', '/api/ruta-invalida', [], $this->pdo);

        $this->assertArrayHasKey('error', $resultado);
        $this->assertEquals('Ruta de cliente no encontrada.', $resultado['error']);
    }
}
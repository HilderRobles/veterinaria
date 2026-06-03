<?php

namespace Tests\Cliente\Infraestructura;

use App\Cliente\Infraestructura\PdoRepositorioCliente;
use App\Cliente\Dominio\Cliente;
use App\Cliente\Dominio\ObjetoValor\ClienteId;
use App\Cliente\Dominio\ObjetoValor\CorreoElectronico;
use App\Cliente\Dominio\ObjetoValor\Telefono;
use App\Cliente\Dominio\ObjetoValor\Contrasena;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;

#[CoversClass(PdoRepositorioCliente::class)]
#[UsesClass(Cliente::class)]
#[UsesClass(ClienteId::class)]
#[UsesClass(CorreoElectronico::class)]
#[UsesClass(Telefono::class)]
#[UsesClass(Contrasena::class)]
class PdoRepositorioClienteTest extends TestCase {
    private \PDO $pdo;
    private PdoRepositorioCliente $repositorio;

    protected function setUp(): void {
        $dependencies = require __DIR__ . '/../../../bootstrap.php';
        $this->pdo = $dependencies['database'];
        $this->pdo->beginTransaction();
        $this->repositorio = new PdoRepositorioCliente($this->pdo);
    }

    protected function tearDown(): void {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }

    public function test_debe_guardar_y_buscar_un_cliente_con_valores_por_defecto(): void {
        $emailUnico = 'defecto_' . uniqid() . '@railway.com';
        // 🟢 Corregido: Teléfono celular local válido (9 dígitos, empieza con 9)
        $cliente = new Cliente("John Doe", new CorreoElectronico($emailUnico), new Telefono("911111111"), new Contrasena("Clave123"));
        
        $this->repositorio->guardar($cliente);
        $buscado = $this->repositorio->buscarPorCorreoElectronico(new CorreoElectronico($emailUnico));
        
        $this->assertNotNull($buscado);
        $this->assertEquals("cliente", $buscado->obtenerRol());
    }

    public function test_debe_guardar_y_buscar_un_cliente_por_id_y_email(): void {
        $emailUnico = 'test_' . uniqid() . '@railway.com';
        // 🟢 Corregido: Teléfono celular local válido (9 dígitos, empieza con 9)
        $cliente = new Cliente("Barry Allen", new CorreoElectronico($emailUnico), new Telefono("923456789"), new Contrasena("Hash123"), "veterinario", null);

        $this->repositorio->guardar($cliente);
        $buscadoEmail = $this->repositorio->buscarPorCorreoElectronico(new CorreoElectronico($emailUnico));
        $buscadoId = $this->repositorio->buscarPorId($buscadoEmail->obtenerId());

        $this->assertNotNull($buscadoId);
        $this->assertEquals("Barry Allen", $buscadoId->obtenerNombre());
    }

    public function test_debe_actualizar_datos_de_contacto_correctamente(): void {
        $emailOriginal = 'oliver_' . uniqid() . '@queen.com';
        $emailNuevo = 'greenarrow_' . uniqid() . '@star.com';
        // 🟢 Corregido: Teléfono celular local válido (9 dígitos, empieza con 9)
        $this->repositorio->guardar(new Cliente("Oliver Queen", new CorreoElectronico($emailOriginal), new Telefono("955123456"), new Contrasena("SecretArrow")));

        $cliente = $this->repositorio->buscarPorCorreoElectronico(new CorreoElectronico($emailOriginal));
        // 🟢 Corregido: Teléfono celular local válido para la actualización (9 dígitos, empieza con 9)
        $this->repositorio->actualizarDatosContacto($cliente->obtenerId(), "Oliver Modificado", new CorreoElectronico($emailNuevo), new Telefono("955987654"));

        $modificado = $this->repositorio->buscarPorId($cliente->obtenerId());
        $this->assertEquals("Oliver Modificado", $modificado->obtenerNombre());
    }

    public function test_debe_eliminar_un_cliente_por_completo(): void {
        $email = 'bruce_' . uniqid() . '@wayne.corp';
        // 🟢 Corregido: Teléfono celular local válido (9 dígitos, empieza con 9)
        $this->repositorio->guardar(new Cliente("Bruce Wayne", new CorreoElectronico($email), new Telefono("999999999"), new Contrasena("IAmBatman")));

        $cliente = $this->repositorio->buscarPorCorreoElectronico(new CorreoElectronico($email));
        $this->repositorio->eliminar($cliente->obtenerId());

        $this->assertNull($this->repositorio->buscarPorId($cliente->obtenerId()));
    }

    public function test_buscar_por_id_debe_retornar_null_si_no_existe(): void {
        $this->assertNull($this->repositorio->buscarPorId(new ClienteId(99999)));
    }

    public function test_buscar_por_correo_debe_retornar_null_si_no_existe(): void {
        $this->assertNull($this->repositorio->buscarPorCorreoElectronico(new CorreoElectronico("no_existe@test.com")));
    }

    public function test_debe_buscar_todos_los_clientes_existentes(): void {
        $email1 = 'todos1_' . uniqid() . '@test.com';
        $email2 = 'todos2_' . uniqid() . '@test.com';

        // 🟢 Corregido: Teléfonos celulares locales válidos (9 dígitos, empiezan con 9)
        $this->repositorio->guardar(new Cliente("Cliente Uno", new CorreoElectronico($email1), new Telefono("912345678"), new Contrasena("h1"), "cliente"));
        $this->repositorio->guardar(new Cliente("Cliente Dos", new CorreoElectronico($email2), new Telefono("987654321"), new Contrasena("h2"), "cliente"));

        $lista = $this->repositorio->buscarTodos();

        $this->assertIsArray($lista);
        $this->assertGreaterThanOrEqual(2, count($lista));
        $this->assertInstanceOf(Cliente::class, $lista[0]);
    }
}
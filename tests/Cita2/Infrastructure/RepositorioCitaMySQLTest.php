<?php

namespace App\Tests\Cita\Infrastructure;

use PHPUnit\Framework\TestCase;
use App\Cita\Infrastructure\RepositorioCitaMySQL;
use App\Cita\Domain\Cita;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PDO;

#[CoversClass(RepositorioCitaMySQL::class)]
#[UsesClass(Cita::class)]
class RepositorioCitaMySQLTest extends TestCase
{
    private PDO $pdo;
    private RepositorioCitaMySQL $repositorio;

    protected function setUp(): void
    {
        try {
            // 🎯 Capturamos el inicio del kernel (bootstrap.php en la raíz)
            $kernel = require __DIR__ . '/../../../bootstrap.php';

            // Extraemos la conexión PDO global generada por tu bootstrap
            $this->pdo = $kernel['database'];

            // 🛠️ SOLUCIÓN AL ERROR 1701: Burlamos temporalmente las llaves foráneas
            $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
            
            // Ahora Railway te dejará vaciar la tabla 'citas' sin protestar
            $this->pdo->exec("TRUNCATE TABLE citas");
            
            // Reactivamos la seguridad de la base de datos de inmediato
            $this->pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

            // Instanciamos el repositorio usando la conexión global compartida
            $this->repositorio = new RepositorioCitaMySQL($this->pdo);

        } catch (\PDOException $e) {
            // Si el internet falla o las credenciales expiran, detiene el test mostrando la razón real
            $this->fail("Fallo crítico de conexión en el test de infraestructura: " . $e->getMessage());
        }
    }

    // =========================================================================
    // 1. PROBANDO: guardar()
    // =========================================================================
    public function testGuardarInsertaLaCitaCorrectamenteEnLaBaseDeDatos(): void
    {
        $cita = new Cita("Carlos Gomez", "Firulais", "2026-06-15", "10:30:00");

        $resultado = $this->repositorio->guardar($cita);

        $this->assertEquals("Carlos Gomez", $resultado['cliente_nombre']);
        $this->assertEquals("Firulais", $resultado['mascota_nombre']);

        // Verificación física directa en Railway usando el PDO del kernel
        $stmt = $this->pdo->query("SELECT * FROM citas WHERE cliente_nombre = 'Carlos Gomez'");
        $dbData = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertNotFalse($dbData);
        $this->assertEquals("pendiente", $dbData['estado']);
    }

    // =========================================================================
    // 2. PROBANDO: buscarPorId()
    // =========================================================================
    public function testBuscarPorIdDevuelveElRegistroCorrecto(): void
    {
        // Insertamos un registro con ID controlado (42) para evaluar el SELECT
        $stmt = $this->pdo->prepare("INSERT INTO citas (id, cliente_nombre, mascota_nombre, fecha, hora, estado) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([42, "Maria Lopez", "Michi", "2026-06-16", "16:00:00", "pendiente"]);

        $resultado = $this->repositorio->buscarPorId(42);

        $this->assertIsArray($resultado);
        $this->assertEquals("Michi", $resultado['mascota_nombre']);
    }

    // =========================================================================
    // 3. PROBANDO: actualizar()
    // =========================================================================
    public function testActualizarModificaElEstadoDeLaCita(): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO citas (id, cliente_nombre, mascota_nombre, fecha, hora, estado) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([1, "Juan Perez", "Toby", "2026-06-17", "09:00:00", "pendiente"]);

        $cita = new Cita("Juan Perez", "Toby", "2026-06-17", "09:00:00", 1);
        $cita->confirmar(); 

        $this->repositorio->actualizar($cita);

        // Validamos el cambio directamente en Railway
        $stmt = $this->pdo->query("SELECT estado FROM citas WHERE id = 1");
        $this->assertEquals("confirmada", $stmt->fetchColumn());
    }

    // =========================================================================
    // 4. PROBANDO: listar()
    // =========================================================================
    public function testListarDevuelveTodosLosRegistrosDeLaTabla(): void
    {
        $this->pdo->exec("INSERT INTO citas (cliente_nombre, mascota_nombre, fecha, hora, estado) VALUES ('Ana', 'Kira', '2026-06-18', '08:00', 'pendiente')");
        $this->pdo->exec("INSERT INTO citas (cliente_nombre, mascota_nombre, fecha, hora, estado) VALUES ('Luis', 'Rocky', '2026-06-19', '14:00', 'pendiente')");

        $resultados = $this->repositorio->listar();

        $this->assertIsArray($resultados);
        $this->assertCount(2, $resultados);
    }
}
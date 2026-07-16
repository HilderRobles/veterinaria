<?php
declare(strict_types=1);
namespace Tests\Cita\Infraestructura;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Cita\Infraestructura\PdoRepositorioCita;
use App\Cita\Dominio\Cita;
use PDO;

#[CoversClass(PdoRepositorioCita::class)]
final class PdoRepositorioCitaTest extends TestCase
{
    private PDO $pdo;
    private PdoRepositorioCita $repositorio;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $this->pdo->exec("
            CREATE TABLE citas (
                id_cita INTEGER PRIMARY KEY AUTOINCREMENT,
                id_cliente INTEGER, id_mascota INTEGER, id_servicio INTEGER,
                fecha TEXT, hora TEXT, motivo TEXT, estado_cita TEXT
            )
        ");

        $this->repositorio = new PdoRepositorioCita($this->pdo);
    }

    public function test_guarda_y_busca_cita(): void
    {
        $cita = Cita::crearNueva(1, 1, 1, '2028-12-10', '10:00:00', 'Chequeo');
        $this->repositorio->guardar($cita);

        $guardada = $this->repositorio->buscarPorId(1);
        $this->assertNotNull($guardada);
        $this->assertSame(1, $guardada->getIdCliente());
    }

    public function test_lista_citas(): void
    {
        $cita = Cita::crearNueva(1, 1, 1, '2028-12-10', '10:00:00', 'Chequeo');
        $this->repositorio->guardar($cita);

        $citas = $this->repositorio->listarTodas();
        $this->assertCount(1, $citas);
    }
}
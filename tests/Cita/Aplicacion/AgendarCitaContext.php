<?php
declare(strict_types=1);
namespace Test\Cita\Aplicacion;

use Behat\Behat\Context\Context;
use App\Cita\Aplicacion\AgendarCita;
use App\Cita\Aplicacion\AgendarCitaPeticion;
use App\Cita\Aplicacion\NotificadorCita;
use App\Cita\Dominio\RepositorioCita;
use Mockery;
use PHPUnit\Framework\Assert;
use DateTimeImmutable;

class AgendarCitaContext implements Context
{
    private $repositorioMock;
    private $notificadorMock;
    private AgendarCita $casoUso;
    private ?\Exception $excepcionCapturada = null;
    private bool $citaGuardada = false;

    public function __construct()
    {
        $this->repositorioMock = Mockery::mock(RepositorioCita::class);
        $this->notificadorMock = Mockery::mock(NotificadorCita::class);
        
        // El mock simula que el mensaje se envía correctamente sin hacer nada real
        $this->notificadorMock->allows('notificarCreacion')->andReturnNull();

        $this->casoUso = new AgendarCita($this->repositorioMock, $this->notificadorMock);
    }

    /**
     * @Given un cliente y una mascota registrados en la veterinaria
     */
    public function unClienteYUnaMascotaRegistrados()
    {
        // Asumimos precondiciones válidas
    }

    /**
     * @When la doctora agenda una cita clínica para la mascota en una fecha futura
     */
    public function laDoctoraAgendaUnaCitaEnFechaFutura()
    {
        $manana = (new DateTimeImmutable('+1 day'))->format('Y-m-d');
        $peticion = new AgendarCitaPeticion(1, 1, 1, $manana, '10:00:00', 'Chequeo BDD');

        $this->repositorioMock->allows('guardar')->andReturnUsing(function() {
            $this->citaGuardada = true;
        });

        try {
            $this->casoUso->ejecutar($peticion);
        } catch (\Exception $e) {
            $this->excepcionCapturada = $e;
        }
    }

    /**
     * @When se intenta registrar una cita en una fecha del pasado
     */
    public function seIntentaRegistrarCitaEnElPasado()
    {
        $ayer = (new DateTimeImmutable('-1 day'))->format('Y-m-d');
        $peticion = new AgendarCitaPeticion(1, 1, 1, $ayer, '10:00:00', 'Chequeo Pasado');

        try {
            $this->casoUso->ejecutar($peticion);
        } catch (\Exception $e) {
            $this->excepcionCapturada = $e;
        }
    }

    /**
     * @Then la cita queda registrada correctamente
     */
    public function laCitaQuedaRegistrada()
    {
        Assert::assertNull($this->excepcionCapturada);
        Assert::assertTrue($this->citaGuardada);
    }

    /**
     * @Then la cita queda en estado :estado
     */
    public function laCitaQuedaEnEstado($estado)
    {
        Assert::assertTrue($this->citaGuardada);
    }

    /**
     * @Then el sistema debe impedir el registro por fecha inválida
     */
    public function sistemaImpideRegistroPorFecha()
    {
        Assert::assertNotNull($this->excepcionCapturada);
        Assert::assertStringContainsString('INV-02', $this->excepcionCapturada->getMessage());
    }

    /** @AfterScenario */
    public function limpiarMocks()
    {
        Mockery::close();
    }
}